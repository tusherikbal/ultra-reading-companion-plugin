document.addEventListener('DOMContentLoaded', function() {
    const progressBar = document.getElementById('urc-progress');
    const scrollBtn = document.getElementById('urc-scroll-top');

    window.onscroll = function() {
        // Calculation for progress bar
        let winScroll = window.scrollY || document.documentElement.scrollTop;
        let height = document.documentElement.scrollHeight - document.documentElement.clientHeight;
        let scrolled = (winScroll / height) * 100;

        if (progressBar) {
            progressBar.style.width = scrolled + "%";
        }

        // Scroll to top button visibility
        if (scrollBtn) {
            if (winScroll > 300) {
                scrollBtn.style.display = "flex";
            } else {
                scrollBtn.style.display = "none";
            }
        }
    };

    if (scrollBtn) {
        scrollBtn.onclick = function() {
            window.scrollTo({ top: 0, behavior: 'smooth' });
        };
    }
});




// document.addEventListener('DOMContentLoaded', function() {
//     const progressBar = document.getElementById('urc-progress');
//     const scrollBtn = document.getElementById('urc-scroll-top');

//     function updateProgress() {
//         const winScroll = document.body.scrollTop || document.documentElement.scrollTop;
//         const height = document.documentElement.scrollHeight - document.documentElement.clientHeight;
//         const scrolled = (height > 0) ? (winScroll / height) * 100 : 0;
//         if (progressBar) progressBar.style.width = scrolled + "%";

//         if (scrollBtn) {
//             if (winScroll > 300) scrollBtn.style.display = "block";
//             else scrollBtn.style.display = "none";
//         }
//     }

//     // Use addEventListener to avoid overwriting other handlers
//     window.addEventListener('scroll', updateProgress, { passive: true });

//     // Initial calculation in case the page was already scrolled
//     updateProgress();

//     if (scrollBtn) {
//         scrollBtn.addEventListener('click', function() {
//             window.scrollTo({ top: 0, behavior: 'smooth' });
//         });
//     }
// //
//     // Debug marker to confirm script is loaded
//     if (typeof console !== 'undefined') console.debug('urc-script loaded');
// });