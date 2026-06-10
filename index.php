<?php
session_start();
include 'config/db.php';
include_once 'includes/db-setup.php';
include_once 'includes/helpers.php';

$total_items = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) AS total FROM items"));
$total_users = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) AS total FROM users"));
$total_donations = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) AS total FROM items WHERE status='Shared'"));
$featured_items = mysqli_query($conn, "SELECT * FROM items WHERE status='Available' AND quantity > 0 ORDER BY created_at DESC LIMIT 6");
$is_logged_in = isset($_SESSION['user_id']);
$nearby_items = false;
$nearby_recipients = false;
$user_address = [];

if($is_logged_in)
{
    $user_id = (int)$_SESSION['user_id'];
    $user_address = getUserAddress($conn, $user_id);
    $nearby_items = getNearbyItemsForUser($conn, $user_id, 4);
    $nearby_recipients = getNearbyRecipientsForDonor($conn, $user_id, 4);
}
?>
<?php include 'includes/header.php'; ?>
<?php include 'includes/navbar.php'; ?>

<section class="hero">
    <div class="container">
        <div class="hero-badge">Community Sharing Platform</div>
        <h1>Share More, <span class="hero-highlight">Waste Less</span></h1>
        <p class="lead mt-3 mx-auto hero-subtitle">
            Give unused items a second life. Connect with donors and recipients in your neighbourhood.
        </p>

        <div class="home-hero-stats">
            <div class="home-hero-stat">
                <strong><?php echo (int)$total_items['total']; ?>+</strong>
                <span>Items Listed</span>
            </div>
            <div class="home-hero-stat">
                <strong><?php echo (int)$total_users['total']; ?>+</strong>
                <span>Members</span>
            </div>
            <div class="home-hero-stat">
                <strong><?php echo (int)$total_donations['total']; ?>+</strong>
                <span>Shares Done</span>
            </div>
        </div>

        <div class="mt-4 hero-actions">
            <?php if($is_logged_in){ ?>
            <a href="items/browse.php" class="btn hero-btn-primary btn-lg me-2 mb-2">Browse Items</a>
            <a href="items/create.php" class="btn hero-btn-secondary btn-lg mb-2">Donate Item</a>
            <?php } else { ?>
            <a href="auth/register.php" class="btn hero-btn-primary btn-lg me-2 mb-2">Join Free</a>
            <a href="auth/login.php" class="btn hero-btn-secondary btn-lg mb-2">Login</a>
            <?php } ?>
        </div>
    </div>
</section>

<?php if($is_logged_in && $nearby_items && mysqli_num_rows($nearby_items) > 0){ ?>
<section class="container py-5">
    <div class="section-heading">
        <h2 class="section-title mb-2">📍 Nearby Items For You</h2>
        <p class="text-muted text-center mb-4">Matched to your address: <?php echo htmlspecialchars(formatUserAddress($user_address)); ?></p>
    </div>
    <div class="row g-4">
        <?php while($item = mysqli_fetch_assoc($nearby_items)){
            $images = getItemImages($conn, $item['id']);
        ?>
        <div class="col-md-3">
            <div class="featured-item-card">
                <?php if(!empty($images)){ ?>
                <img src="uploads/<?php echo htmlspecialchars($images[0]); ?>" alt="<?php echo htmlspecialchars($item['title']); ?>">
                <?php } else { ?>
                <div class="featured-item-placeholder">📦</div>
                <?php } ?>
                <div class="p-3">
                    <h5><?php echo htmlspecialchars($item['title']); ?></h5>
                    <p class="mb-2 text-muted small"><?php echo htmlspecialchars($item['category']); ?></p>
                    <p class="mb-2">📍 <?php echo htmlspecialchars($item['city']); ?>, <?php echo htmlspecialchars($item['locality']); ?></p>
                    <span class="badge bg-success"><?php echo (int)$item['quantity']; ?> available</span>
                    <a href="items/view.php?id=<?php echo $item['id']; ?>" class="btn btn-success btn-sm mt-2">View Item</a>
                </div>
            </div>
        </div>
        <?php } ?>
    </div>
</section>
<?php } ?>

<?php if($is_logged_in && $nearby_recipients && mysqli_num_rows($nearby_recipients) > 0){ ?>
<section class="container pb-5">
    <div class="nearby-users-panel">
        <h3 class="mb-2">👥 Nearby Recipients in Your Area</h3>
        <p class="text-muted mb-4">Community members near you who may benefit from your donations.</p>
        <div class="row g-3">
            <?php while($recipient = mysqli_fetch_assoc($nearby_recipients)){ ?>
            <div class="col-md-3">
                <div class="nearby-user-card">
                    <div class="nearby-user-avatar"><?php echo strtoupper(substr($recipient['full_name'], 0, 1)); ?></div>
                    <h6 class="mb-1"><?php echo htmlspecialchars($recipient['full_name']); ?></h6>
                    <p class="text-muted small mb-0">📍 <?php echo htmlspecialchars(formatUserAddress($recipient)); ?></p>
                </div>
            </div>
            <?php } ?>
        </div>
        <div class="text-center mt-4">
            <a href="items/create.php" class="btn btn-success">Donate to Someone Nearby</a>
        </div>
    </div>
</section>
<?php } ?>

<?php if(mysqli_num_rows($featured_items) > 0){ ?>
<section class="container py-5 bg-section">
    <h2 class="section-title">Latest Available Items</h2>
    <div class="row g-4">
        <?php while($item = mysqli_fetch_assoc($featured_items)){
            $images = getItemImages($conn, $item['id']);
        ?>
        <div class="col-md-4">
            <div class="featured-item-card">
                <?php if(!empty($images)){ ?>
                <img src="uploads/<?php echo htmlspecialchars($images[0]); ?>" alt="<?php echo htmlspecialchars($item['title']); ?>">
                <?php } else { ?>
                <div class="featured-item-placeholder">📦</div>
                <?php } ?>
                <div class="p-3">
                    <h5><?php echo htmlspecialchars($item['title']); ?></h5>
                    <p class="mb-2 text-muted"><?php echo htmlspecialchars($item['category']); ?> · <?php echo (int)$item['quantity']; ?> available</p>
                    <p class="mb-3">📍 <?php echo htmlspecialchars($item['city']); ?>, <?php echo htmlspecialchars($item['locality']); ?></p>
                    <?php if($is_logged_in){ ?>
                    <a href="items/view.php?id=<?php echo $item['id']; ?>" class="btn btn-success btn-sm">View Item</a>
                    <?php } else { ?>
                    <a href="auth/login.php" class="btn btn-success btn-sm">Login to Request</a>
                    <?php } ?>
                </div>
            </div>
        </div>
        <?php } ?>
    </div>
</section>
<?php } ?>

<section class="container py-5" id="categories">
    <h2 class="section-title">Explore Categories</h2>
    <div class="row g-4">
        <?php
        $categories = [
            ['icon' => '👕', 'name' => 'Clothes'],
            ['icon' => '💄', 'name' => 'Makeup'],
            ['icon' => '💍', 'name' => 'Jewellery'],
            ['icon' => '🏠', 'name' => 'Household Items'],
            ['icon' => '🎀', 'name' => 'Decoration Items'],
            ['icon' => '🍲', 'name' => 'Food'],
            ['icon' => '💊', 'name' => 'Medicines'],
            ['icon' => '🍳', 'name' => 'Kitchen Items'],
            ['icon' => '📱', 'name' => 'Electronic Items'],
            ['icon' => '📚', 'name' => 'Books'],
            ['icon' => '🪑', 'name' => 'Furniture'],
            ['icon' => '📦', 'name' => 'Others'],
        ];
        foreach($categories as $cat){
            $browse_url = $is_logged_in ? 'items/browse.php?category=' . urlencode($cat['name']) : 'auth/login.php';
        ?>
        <div class="col-6 col-md-3">
            <a href="<?php echo $browse_url; ?>" class="category-card-link">
            <div class="category-card">
                <div class="category-icon"><?php echo $cat['icon']; ?></div>
                <h5 class="mt-2 mb-0"><?php echo htmlspecialchars($cat['name']); ?></h5>
            </div>
            </a>
        </div>
        <?php } ?>
    </div>
</section>

<section class="container py-5" id="how-it-works">
    <h2 class="section-title">How It Works</h2>
    <div class="row g-4 text-center">
        <div class="col-md-4">
            <div class="feature-step-card">
                <div class="feature-step-num">1</div>
                <h5>Post Item</h5>
                <p>Upload photos, set quantity, and let AI suggest the right category.</p>
            </div>
        </div>
        <div class="col-md-4">
            <div class="feature-step-card">
                <div class="feature-step-num">2</div>
                <h5>Match Nearby</h5>
                <p>We recommend recipients and items based on registered addresses.</p>
            </div>
        </div>
        <div class="col-md-4">
            <div class="feature-step-card">
                <div class="feature-step-num">3</div>
                <h5>Share & Earn Badges</h5>
                <p>Chat, coordinate pickup, and earn Bronze, Silver, or Gold donor badges.</p>
            </div>
        </div>
    </div>
</section>

<section class="stats">
    <div class="container">
        <div class="row text-center g-4">
            <div class="col-md-4">
                <div class="stat-box">
                    <h2><?php echo (int)$total_items['total']; ?>+</h2>
                    <p>Items Listed</p>
                </div>
            </div>
            <div class="col-md-4">
                <div class="stat-box">
                    <h2><?php echo (int)$total_users['total']; ?>+</h2>
                    <p>Community Members</p>
                </div>
            </div>
            <div class="col-md-4">
                <div class="stat-box">
                    <h2><?php echo (int)$total_donations['total']; ?>+</h2>
                    <p>Successful Shares</p>
                </div>
            </div>
        </div>
    </div>
</section>

<section class="container py-5">
    <h2 class="section-title">Why Choose ShareCycle?</h2>
    <div class="row g-4">
        <div class="col-md-3">
            <div class="why-card">
                <div class="why-icon">♻️</div>
                <h5>Reduce Waste</h5>
                <p>Keep usable items out of landfills and in circulation.</p>
            </div>
        </div>
        <div class="col-md-3">
            <div class="why-card">
                <div class="why-icon">📍</div>
                <h5>Local Matching</h5>
                <p>Find donors and recipients in your city and locality.</p>
            </div>
        </div>
        <div class="col-md-3">
            <div class="why-card">
                <div class="why-icon">💬</div>
                <h5>Built-in Chat</h5>
                <p>Message directly before accepting or picking up items.</p>
            </div>
        </div>
        <div class="col-md-3">
            <div class="why-card">
                <div class="why-icon">🏅</div>
                <h5>Donor Badges</h5>
                <p>Earn recognition as you complete more shares.</p>
            </div>
        </div>
    </div>
</section>

<section class="container py-5">
    <h2 class="section-title">What People Say</h2>
    <div class="row g-4">
        <div class="col-md-6">
            <div class="testimonial">
                <p>"I found books for my studies through ShareCycle. The nearby match made pickup so easy."</p>
                <b>- Priya, Recipient</b>
            </div>
        </div>
        <div class="col-md-6">
            <div class="testimonial">
                <p>"My unused clothes reached someone in my locality. The donor badge motivated me to give more."</p>
                <b>- Rahul, Gold Donor</b>
            </div>
        </div>
    </div>
</section>

<section class="cta-section text-center">
    <div class="container py-5">
        <h2 class="text-white mb-3">Ready to Make a Difference?</h2>
        <p class="text-white-50 mb-4">Join your community and start sharing today.</p>
        <a href="<?php echo $is_logged_in ? 'items/create.php' : 'auth/register.php'; ?>" class="btn btn-light btn-lg">
            <?php echo $is_logged_in ? 'Donate an Item' : 'Get Started Free'; ?>
        </a>
    </div>
</section>

<?php include 'includes/footer.php'; ?>

<?php include 'includes/footer.php'; ?>
<?php include 'includes/footer.php'; ?>
<?php include 'includes/footer.php'; ?>
<?php include 'includes/footer.php'; ?>