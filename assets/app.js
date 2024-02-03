import 'bootstrap/dist/css/bootstrap.min.css';
import 'bootstrap-icons/font/bootstrap-icons.min.css'
import './styles/app.css';

import './vendor/bootstrap/bootstrap.index.js';

if (document.querySelector('#index-page')) {
    import('./posts.js');
}

if (document.querySelector('#user-page')) {
    import('./user.js');
}

if (document.querySelector('#tag-page')) {
    import('./tag.js');
}

(() => {
    document.addEventListener('click', async event => {
        const likeButton = event.target.closest('a.app-like-post');
        if (likeButton) {
            const likes = likeButton.querySelector('.app-post-likes');
            const heart = likes.querySelector('i');
            if (heart.classList.contains('bi-heart-fill')) {
                heart.classList.remove('bi-heart-fill', 'text-danger');
                heart.classList.add('bi-heart');
                likes.lastChild.nodeValue = ' ' + (parseInt(likes.lastChild.nodeValue) - 1).toString();
            } else {
                heart.classList.remove('bi-heart');
                heart.classList.add('bi-heart-fill', 'text-danger');
                likes.lastChild.nodeValue = ' ' + (parseInt(likes.lastChild.nodeValue) + 1).toString();
            }

            await fetch(`/api/posts/${likeButton.dataset.id}/like`, {method: 'POST'});
        }
    });

    const search = document.querySelector('#app-search');
    search.addEventListener('input', async () => {
        const searchResults = await (await fetch(`/api/search?query=${search.value}`)).json();
        const searchResultsList = document.querySelector('#app-search-results .list-group');

        searchResultsList.innerHTML = '';

        document.querySelector('#app-search-results').classList.remove('d-none');
        for (const result of searchResults) {
            const resultHtml = `<a href="${result.url}"><li class="list-group-item">${result.label}</li></a>`;
            searchResultsList.insertAdjacentHTML('beforeend', resultHtml);
        }
    });

    document.addEventListener('click', event => {
        if (event.target.closest('#app-search') === null) {
            document.querySelector('#app-search-results').classList.add('d-none');
        }
    });
})();
