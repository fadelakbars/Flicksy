document.querySelectorAll('.fa-heart').forEach(icon => {
    icon.addEventListener('click', function () {
        const videoId = this.dataset.videoId;
        fetch('like_video.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
            body: `video_id=${videoId}`
        })
        .then(response => response.json())
        .then(data => {
            if (data.status === 'liked') {
                alert('Video di-like!');
            } else {
                alert('Like dibatalkan.');
            }
        });
    });
});

document.querySelectorAll('.fa-download').forEach(icon => {
    icon.addEventListener('click', function () {
        const videoId = this.dataset.videoId;
        window.location.href = `download_video.php?video_id=${videoId}`;
    });
});
