(() => {
    loadUserPosts();
    loadUserLikedPosts();

    let userPostsCurrentPage = 1;
    const loadMoreUserPosts = document.querySelector('#user-posts .app-load-more-posts');
    loadMoreUserPosts.addEventListener('click', async () => {
        const loader = document.querySelector('#user-posts .app-more-posts-loader');

        loadMoreUserPosts.classList.add('d-none');
        loader.classList.remove('d-none');

        userPostsCurrentPage += 1;
        await loadAndRenderUserPosts(userPostsCurrentPage);

        loader.classList.add('d-none');
    });

    let userLikedPostsCurrentPage = 1;
    const loadMoreUserLikedPosts = document.querySelector('#user-liked-posts .app-load-more-posts');
    loadMoreUserLikedPosts.addEventListener('click', async () => {
        const loader = document.querySelector('#user-liked-posts .app-more-posts-loader');

        loadMoreUserLikedPosts.classList.add('d-none');
        loader.classList.remove('d-none');

        userLikedPostsCurrentPage += 1;
        await loadAndRenderUserLikedPosts(userLikedPostsCurrentPage);

        loader.classList.add('d-none');
    });

    const followUser = document.querySelector('.app-follow-user');
    if (followUser !== null) {
        followUser.addEventListener('click', async () => {
            const username = document.querySelector('#user-page').dataset.username;
            const followersCount = document.querySelector('.app-followers-count');

            if (followUser.innerHTML.trim() === 'Obserwuj') {
                followUser.innerHTML = 'Przestań obserwować';
                followersCount.innerHTML = (parseInt(followersCount.innerHTML)+1).toString();
            } else {
                followUser.innerHTML = 'Obserwuj';
                followersCount.innerHTML = (parseInt(followersCount.innerHTML)-1).toString();
            }

            await fetch(`/api/users/${username}/follow`, {method: 'POST'});
        });
    }

    async function loadUserPosts() {
        const loader = document.querySelector('.app-user-posts-loader');
        const posts = document.querySelector('#user-posts .posts');
        posts.classList.add('d-none');
        posts.innerHTML = '';

        await loadAndRenderUserPosts(1);

        loader.classList.add('d-none');
        posts.classList.remove('d-none');
    }

    async function loadAndRenderUserPosts(page) {
        const posts = document.querySelector('#user-posts .posts');
        const username = document.querySelector('#user-page').dataset.username;
        const postsResponse = await (await fetch(`/api/users/${username}/posts?page=${page}`)).json();

        if (postsResponse.length === 0) {
            loadMoreUserPosts.classList.add('d-none');
            return;
        }

        for (const post of postsResponse) {
            posts.insertAdjacentHTML('beforeend', buildPostHtml(post));
        }

        loadMoreUserPosts.classList.remove('d-none');
    }

    async function loadUserLikedPosts() {
        const loader = document.querySelector('.app-user-liked-posts-loader');
        const posts = document.querySelector('#user-liked-posts .posts');
        posts.classList.add('d-none');
        posts.innerHTML = '';

        await loadAndRenderUserLikedPosts(1);

        loader.classList.add('d-none');
        posts.classList.remove('d-none');
    }

    async function loadAndRenderUserLikedPosts(page) {
        const posts = document.querySelector('#user-liked-posts .posts');
        const username = document.querySelector('#user-page').dataset.username;
        const postsResponse = await (await fetch(`/api/users/${username}/liked-posts?page=${page}`)).json();

        if (postsResponse.length === 0) {
            loadMoreUserLikedPosts.classList.add('d-none');
            return;
        }

        for (const post of postsResponse) {
            posts.insertAdjacentHTML('beforeend', buildPostHtml(post));
        }

        loadMoreUserLikedPosts.classList.remove('d-none');
    }

    function buildPostHtml(post) {
        return `
            <div class="card mb-3">
                <div class="card-body">
                    <div class="card-title d-flex justify-content-between">
                        <div class="fw-bold"><a class="text-decoration-none text-body" href="/users/${post.username}">@${post.username}</a></div>
                        <div class="fw-lighter fst-italic">${post.createdAt}</div>
                    </div>
                    <div class="card-text">${post.content}</div>
                    <div class="d-flex mt-3 app-post-reactions">
                        <a href="/posts/${post.id}">
                            <div class="me-3 app-post-comments"><i class="bi bi-chat-fill me-1"></i> ${post.commentsCount}</div>
                        </a>
                        <a class="app-like-post" href="javascript:void(0)" data-id="${post.id}">
                            <div class="app-post-likes"><i class="bi bi-heart${post.isLikedByUser ? '-fill text-danger' : ''} me-1"></i> ${post.likesCount}</div>
                        </a>
                    </div>
                </div>
            </div>
        `;
    }
})();
