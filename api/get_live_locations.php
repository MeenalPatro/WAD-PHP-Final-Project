<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
require_once '../config/database.php';

if ($_SERVER['REQUEST_METHOD'] == 'GET') {
    $type = $_GET['type'] ?? 'all';
    
    try {
        $response = ['success' => true, 'data' => []];
        
        // ===== 1. EMERGENCIES (🔴 Red) =====
        if ($type == 'all' || $type == 'emergencies') {
            $emergencies = $db->query("SELECT 
                er.id, er.title, er.request_type, er.priority, er.status,
                er.latitude, er.longitude, er.location_name,
                'emergency' as marker_type,
                CASE er.priority
                    WHEN 'critical' THEN 'danger'
                    WHEN 'high' THEN 'warning'
                    WHEN 'medium' THEN 'info'
                    ELSE 'success'
                END as color,
                u.full_name as reporter_name
                FROM emergency_requests er
                JOIN users u ON er.user_id = u.id
                WHERE er.status IN ('pending', 'assigned', 'in_progress')
                AND er.latitude IS NOT NULL
                ORDER BY FIELD(er.priority, 'critical', 'high', 'medium', 'low')")->fetchAll(PDO::FETCH_ASSOC);
            
            foreach ($emergencies as $e) { $response['data'][] = $e; }
        }
        
        // ===== 2. VOLUNTEERS (🟢 Green) - FROM user_locations =====
        if ($type == 'all' || $type == 'volunteers') {
            $volunteers = $db->query("SELECT 
                u.id, u.full_name, u.phone,
                ul.latitude, ul.longitude,
                ul.location_name,
                ul.last_update,
                'volunteer' as marker_type,
                'success' as color,
                v.availability,
                v.total_tasks_completed,
                v.is_online
                FROM users u
                JOIN volunteers v ON u.id = v.user_id
                JOIN user_locations ul ON u.id = ul.user_id
                WHERE u.is_active = 1 
                AND v.is_online = 1
                AND ul.user_type = 'volunteer'
                AND ul.last_update > DATE_SUB(NOW(), INTERVAL 5 MINUTE)
                AND ul.latitude IS NOT NULL
                AND ul.longitude IS NOT NULL")->fetchAll(PDO::FETCH_ASSOC);
            
            foreach ($volunteers as $v) { $response['data'][] = $v; }
        }
        
        // ===== 3. NGOs & CAMPS (🔵 Blue) =====
        if ($type == 'all' || $type == 'ngos') {
            $ngos = $db->query("SELECT 
                n.id, n.organization_name, n.contact_person,
                n.latitude, n.longitude,
                'ngo' as marker_type,
                'primary' as color,
                n.verified,
                rc.camp_name, rc.camp_type
                FROM ngos n
                LEFT JOIN relief_camps rc ON n.id = rc.ngo_id AND rc.is_active = 1
                WHERE n.latitude IS NOT NULL
                AND n.verified = 1")->fetchAll(PDO::FETCH_ASSOC);
            
            foreach ($ngos as $ngo) { $response['data'][] = $ngo; }
        }
        
        // ===== 4. SAFE CITIZENS (🩵 Cyan) =====
        if ($type == 'all' || $type == 'citizens') {
            $citizens = $db->query("SELECT 
                sc.id, u.full_name,
                sc.latitude, sc.longitude, sc.location_name,
                'citizen' as marker_type,
                'info' as color,
                sc.message, sc.checked_in_at
                FROM safe_checkins sc
                JOIN users u ON sc.user_id = u.id
                WHERE sc.checked_in_at > DATE_SUB(NOW(), INTERVAL 1 HOUR)
                ORDER BY sc.checked_in_at DESC LIMIT 50")->fetchAll(PDO::FETCH_ASSOC);
            
            foreach ($citizens as $c) { $response['data'][] = $c; }
        }
        
        echo json_encode($response);
        
    } catch(Exception $e) {
        echo json_encode(['success' => false, 'message' => $e->getMessage()]);
    }
}
?>