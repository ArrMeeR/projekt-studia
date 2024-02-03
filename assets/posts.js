(() => {
    let currentPage = 1;

    const form = document.querySelector('.app-add-post form');
    refreshPosts(form);

    form.addEventListener('submit', async event => {
        event.preventDefault();

        form.classList.add('was-validated');

        if (!form.checkValidity()) {
            return;
        }

        const loader = document.querySelector('.app-loader');
        const textarea = form.querySelector('textarea');
        const posts = document.querySelector('.app-posts');

        form.classList.add('d-none');
        posts.classList.add('d-none');
        loader.classList.remove('d-none');

        const response = await fetch('/api/posts', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
            },
            body: JSON.stringify({content: textarea.value}),
        });

        if (!response.ok) {
            textarea.value = '';
            form.classList.remove('d-none');
            posts.classList.remove('d-none');
            loader.classList.add('d-none');
            return;
        }

        form.classList.remove('was-validated');
        textarea.value = '';

        await refreshPosts(form);
    });

    const loadMorePosts = document.querySelector('.app-load-more-posts');
    loadMorePosts.addEventListener('click', async () => {
        const loader = document.querySelector('.app-more-posts-loader');

        loadMorePosts.classList.add('d-none');
        loader.classList.remove('d-none');

        currentPage += 1;
        await loadAndRenderPosts(currentPage);

        loader.classList.add('d-none');
    });

    async function refreshPosts(form) {
        const loader = document.querySelector('.app-loader');
        const posts = document.querySelector('.app-posts');
        loader.classList.remove('d-none');
        form.classList.add('d-none');
        posts.classList.add('d-none');
        posts.innerHTML = '';

        await loadAndRenderPosts(1);

        loader.classList.add('d-none');
        form.classList.remove('d-none');
        posts.classList.remove('d-none');
    }

    async function loadAndRenderPosts(page) {
        const posts = document.querySelector('.app-posts');
        const postsResponse = await (await fetch(`/api/posts?page=${page}`)).json();

        if (postsResponse.length === 0) {
            loadMorePosts.classList.add('d-none');
            return;
        }

        for (const post of postsResponse) {
            const postHtml = `
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
            posts.insertAdjacentHTML('beforeend', postHtml);
        }

        loadMorePosts.classList.remove('d-none');
    }
})();
