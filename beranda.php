<?php
session_start();
include 'conn/database.php';

// Pastikan pengguna sudah login
if (!isset($_SESSION['user_id'])) {
    echo 'Anda belum login. Silakan login terlebih dahulu.';
    exit();
}

// Ambil ID pengguna dari session
$user_id = $_SESSION['user_id'];

// Ambil postingan dan detail pengguna dari database
$sql = "SELECT p.*, u.nama_lengkap, u.nama_pengguna, u.profile_picture,
        (SELECT COUNT(*) FROM likes WHERE post_id = p.id) AS like_count,
        (SELECT COUNT(*) FROM comments WHERE post_id = p.id) AS comment_count,
        (SELECT COUNT(*) FROM likes WHERE post_id = p.id AND user_id = ?) AS user_liked
        FROM posts p
        JOIN user u ON p.user_id = u.id
        WHERE p.user_id IN (
            SELECT friend_id FROM friends WHERE user_id = ? 
            UNION 
            SELECT user_id FROM friends WHERE friend_id = ?
        )
        ORDER BY p.created_at DESC";
$stmt = $koneksi->prepare($sql);
$stmt->bind_param('iii', $user_id, $user_id, $user_id);
$stmt->execute();
$posts = $stmt->get_result();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.7/dist/umd/popper.min.js"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
    <title>Beranda</title>
    <style>
        .disabled {
            pointer-events: none;
            opacity: 0.5;
        }
    </style>
</head>
<body class="pt-0 pb-2 pe-3 ps-3 content" id="fullpage">
    <div class="container col-md-5 mt-0 mb-5">
        <!-- header logo -->
        <div class="container mb-3 pt-3">
            <table class="text-center">
            <div class="row">
                <div class="col"></div>
                <div class="col text-center">
                    <img src="assets/logo.png" width="150" alt="">
                </div>
                <div class="col text-end">
                    <a href="talk.php" class="bi bi-chat-left-fill text-black" style="font-size: 25px;"></a>
                </div>
            </div>
            </table>
        </div>

        <!-- postingan -->
        <?php if ($posts->num_rows > 0): ?>
            <?php while ($post = $posts->fetch_assoc()): ?>
                <div class="bg-body-secondary rounded-4 p-4 mb-2">
                    <div>
                        <table>
                            <tr>
                                <th>
                                    <?php if (!empty($post['profile_picture'])): ?>
                                        <img src="uploads/<?php echo htmlspecialchars($post['profile_picture']); ?>" alt="Foto Profil" class="img-fluid rounded-circle me-2" style="width: 50px; height: 50px;">
                                    <?php else: ?>
                                        <img src="assets/default-profile.png" alt="Foto Profil" class="img-fluid rounded-circle me-2" style="width: 50px; height: 50px;">
                                    <?php endif; ?>
                                </th>
                                <th>
                                    <h6 class="fw-bold"><?php echo htmlspecialchars($post['nama_lengkap']); ?> 
                                        <p class="m-0 fw-normal text-body-emphasis" style="font-size: 13px;">@<?php echo htmlspecialchars($post['nama_pengguna']); ?></p>
                                        <label for="name" class="text-body-secondary" style="font-size: 12px;">Di posting pada <?php echo htmlspecialchars($post['created_at']); ?></label>
                                    </h6>
                                </th>
                            </tr>
                        </table>
                    </div>
                    <div>
                        <p><?php echo htmlspecialchars($post['content']); ?></p>
                        <!-- Menampilkan gambar jika ada -->
                           <?php if (!empty($post['image'])): ?>
                                <div class="m-3">
                                    <img src="uploads/<?php echo htmlspecialchars($post['image']); ?>" alt="Gambar Postingan" class="img-fluid rounded post-image" >
                                </div>
                        <?php endif; ?>                    </div>
                    <div class="btn-group d-flex justify-content-between mb-3" role="group" aria-label="interaction">
                        <!-- Tombol Like -->
                        <button class="btn btn-light text-black btn-sm w-100 like-btn" 
                                data-post-id="<?php echo $post['id']; ?>" 
                                data-liked="<?php echo $post['user_liked'] ? '1' : '0'; ?>">
                            <i class="bi <?php echo $post['user_liked'] ? 'bi-heart-fill' : 'bi-heart'; ?>"></i> <span class="like-count"><?php echo $post['like_count']; ?></span>
                        </button>
                        <!-- Tombol Komentar -->
                        <button class="btn btn-light text-black btn-sm w-100 comment-btn" 
                                data-post-id="<?php echo $post['id']; ?>">
                            <i class="bi bi-chat-left<?php echo $post['comment_count'] > 0 ? '-fill' : ''; ?>"></i> <span class="comment-count"><?php echo $post['comment_count']; ?></span>
                        </button>
                    </div>

                    <!-- Bagian Komentar -->
                    <div class="comment-section" id="comments-<?php echo $post['id']; ?>" style="display: none;">
                        <!-- Komentar akan dimuat di sini -->
                    </div>
                    <form class="add-comment-form" data-post-id="<?php echo $post['id']; ?>" style="display: none;">
                        <div class="mb-3">
                            <textarea class="form-control" name="comment_content" placeholder="Tambah komentar..." rows="3"></textarea>
                        </div>
                        <button type="submit" class="btn btn-primary">Kirim</button>
                    </form>
                </div>
            <?php endwhile; ?>
        <?php else: ?>
            <div class="text-center position-absolute top-50 start-50 translate-middle">
                <strong>Tidak ada postingan.</strong> <br>
                Bertemanlah lebih banyak untuk dapat bagian dari kebahagiaan mereka.
            </div>
        <?php endif; ?>

        <!-- footer -->
        <div class="navbar d-flex justify-content-between pt-3 pb-3 pe-5 ps-5 mt-4 bg-secondary rounded-5 sticky-bottom">
            <div>
                <a href="beranda.php" class="text-white bi-house-fill"></a> 
            </div>
            <div>
                <a href="post.php" class="text-white bi-send-fill"></a>
            </div>
            <div>
                <a href="search_friend.php" class="text-white bi-search"></a> 
            </div>
            <div class='text-center'>
                <a href="profil.php?id=<?php echo $_SESSION['user_id']; ?>" class="text-white bi-person-fill"></a> 
                </span>

            </div>
        </div>

        <style>
        .navbar {
            position: fixed;
            bottom: 0;
            width: 85%;
            text-align: center;
            margin-bottom: 5px;
        }
        .post-image {
        max-width: 100%; /* Menyesuaikan lebar gambar dengan kontainer */
        height: auto; /* Menjaga rasio aspek gambar */
        cursor: pointer; /* Menampilkan pointer saat hover */
        display: block; /* Menghindari white space di bawah gambar */
    }
        </style>


    <script>
$(document).ready(function() {
    var debounceTimeout = null;

    // Load comments for each post
    $('.comment-btn').on('click', function() {
        var post_id = $(this).data('post-id');
        var commentSection = $('#comments-' + post_id);
        var commentForm = $(this).closest('.bg-body-secondary').find('.add-comment-form');

        // Toggle visibility of comments and form
        commentSection.toggle();
        commentForm.toggle();

        // Load comments if they are visible
        if (commentSection.is(':visible')) {
            loadComments(post_id);
        }
    });

    // Like button click event
    $('.like-btn').on('click', function() {
        var $this = $(this);
        var post_id = $this.data('post-id');
        var is_liked = $this.data('liked') === 1;

        // Disable the button to prevent multiple clicks
        $this.addClass('disabled');

        if (debounceTimeout) {
            clearTimeout(debounceTimeout);
        }

        debounceTimeout = setTimeout(function() {
            $.ajax({
                url: 'like.php',
                type: 'POST',
                data: {
                    post_id: post_id,
                    action: is_liked ? 'unlike' : 'like'
                },
                success: function(response) {
                    var data = JSON.parse(response);

                    // Update the like button and count
                    $this.find('i').toggleClass('bi-heart-fill bi-heart');
                    $this.data('liked', data.user_liked);
                    $this.find('.like-count').text(data.like_count);
                },
                error: function(xhr, status, error) {
                    console.error('Error:', error);
                },
                complete: function() {
                    // Re-enable the button
                    $this.removeClass('disabled');
                }
            });
        }, 300); // Adjust debounce delay as needed
    });

    // Comment form submit event
    $('.add-comment-form').on('submit', function(event) {
        event.preventDefault();
        var post_id = $(this).data('post-id');
        var comment_content = $(this).find('textarea[name="comment_content"]').val();
        var commentSection = $('#comments-' + post_id);
        var commentCount = $(this).closest('.bg-body-secondary').find('.comment-count');

        $.ajax({
            url: 'comment.php',
            type: 'POST',
            data: {
                post_id: post_id,
                comment_content: comment_content
            },
            success: function(response) {
                // Append the new comment to the comment section
                commentSection.append(response);
                // Clear the textarea
                $(this).find('textarea[name="comment_content"]').val('');

                // Update comment count
                var newCount = parseInt(commentCount.text()) + 1;
                commentCount.text(newCount);
            }.bind(this),
            error: function(xhr, status, error) {
                console.error('Error:', error);
            }
        });
    });

    // Function to load comments for a post
    function loadComments(post_id) {
        $.ajax({
            url: 'load_comments.php',
            type: 'POST',
            data: {
                post_id: post_id
            },
            success: function(response) {
                // Display the comments in the comment section
                $('#comments-' + post_id).html(response);
            },
            error: function(xhr, status, error) {
                console.error('Error:', error);
            }
        });
    }


    $(document).ready(function() {
            $("img").click(function() {
                if (this.requestFullscreen) {
                    this.requestFullscreen();
                } else if (this.mozRequestFullScreen) { // Firefox
                    this.mozRequestFullScreen();
                } else if (this.webkitRequestFullscreen) { // Chrome, Safari and Opera
                    this.webkitRequestFullscreen();
                } else if (this.msRequestFullscreen) { // IE/Edge
                    this.msRequestFullscreen();
                }
            });
        });                });
</script>
</body>
</html>
