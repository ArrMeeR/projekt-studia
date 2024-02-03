(() => {
    loadPosts();

    let currentPage = 1;
    const loadMorePosts = document.querySelector('.app-load-more-posts');
    loadMorePosts.addEventListener('click', async () => {
        const loader = document.querySelector('.app-more-posts-loader');

        loadMorePosts.classList.add('d-none');
        loader.classList.remove('d-none');

        currentPage += 1;
        await loadAndRenderPosts(currentPage);

        loader.classList.add('d-none');
    });

    async function loadPosts() {
        const loader = document.querySelector('.app-loader');
        const posts = document.querySelector('.app-posts');
        posts.classList.add('d-none');
        posts.innerHTML = '';

        await loadAndRenderPosts(1);

        loader.classList.add('d-none');
        posts.classList.remove('d-none');
    }

    async function loadAndRenderPosts(page) {
        const posts = document.querySelector('.app-posts');
        const tag = document.querySelector('#tag-page').dataset.tag;
        const postsResponse = await (await fetch(`/api/tags/${tag}/posts?page=${page}`)).json();

        if (postsResponse.length === 0) {
            loadMorePosts.classList.add('d-none');
            return;
        }

        for (const post of postsResponse) {
            posts.insertAdjacentHTML('beforeend', buildPostHtml(post));
        }

        loadMorePosts.classList.remove('d-none');
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
                        <a href="#">
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
