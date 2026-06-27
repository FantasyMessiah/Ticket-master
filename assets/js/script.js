<script>
const toggle = document.getElementById("menuToggle");
const menu = document.getElementById("mobileMenu");

toggle.addEventListener("click", () => {

    menu.classList.toggle("show");

    const expanded = menu.classList.contains("show");

    toggle.setAttribute("aria-expanded", expanded);
});
</script>
