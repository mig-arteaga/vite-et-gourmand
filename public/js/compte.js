// Gestion des onglets de l'espace client (Mon compte)
export function initCompteTabs() {
    const tabButtons = document.querySelectorAll(".compte-tab-btn");
    const tabPanels = document.querySelectorAll(".compte-panel");

    if (tabButtons.length === 0 || tabPanels.length === 0) {
        return;
    }

    const activateTab = (tabName, updateUrl = true) => {
        tabButtons.forEach((btn) => {
            const isMatch = btn.dataset.compteTab === tabName;
            btn.classList.toggle("active", isMatch);
            btn.setAttribute("aria-selected", isMatch ? "true" : "false");
        });

        tabPanels.forEach((panel) => {
            const isMatch = panel.id === `compte-tab-${tabName}`;
            panel.classList.toggle("active", isMatch);
        });

        if (updateUrl && window.history.replaceState) {
            const url = new URL(window.location.href);
            url.searchParams.set("tab", tabName);
            window.history.replaceState({}, "", url.toString());
        }
    };

    tabButtons.forEach((button) => {
        button.addEventListener("click", () => {
            const targetTab = button.dataset.compteTab;
            if (targetTab) {
                activateTab(targetTab, true);
            }
        });
    });
}

