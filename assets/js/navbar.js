document.addEventListener("DOMContentLoaded", function () {
    const menuBtn = document.querySelector(
        'button[aria-label="Open site navigation menu"]'
    );

    if (!menuBtn) return;

    menuBtn.addEventListener("click", function () {
        const expanded = menuBtn.getAttribute("aria-expanded") === "true";
        menuBtn.setAttribute("aria-expanded", !expanded);

        console.log("clicked");
    });
});
