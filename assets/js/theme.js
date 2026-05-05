document.addEventListener("DOMContentLoaded", () => {
  const panel = document.querySelector(".js-search-panel");

  if (panel) {
    panel.addEventListener("submit", () => {
      panel.classList.add("is-loading");
    });
  }

  document.querySelectorAll(".pill").forEach((pill) => {
    pill.addEventListener("click", () => {
      pill.classList.toggle("is-active");
    });
  });
});

