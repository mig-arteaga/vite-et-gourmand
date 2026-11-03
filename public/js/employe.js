// Gestion des onglets de l'espace employé
export function initEmployeTabs() {
    const tabButtons = document.querySelectorAll(".employe-tab-btn");
    const tabPanels = document.querySelectorAll(".employe-panel");

    if (tabButtons.length === 0 || tabPanels.length === 0) {
        return;
    }

    const activateTab = (tabName, updateUrl = true) => {
        tabButtons.forEach((btn) => {
            const isMatch = btn.dataset.tab === tabName;
            btn.classList.toggle("active", isMatch);
            btn.setAttribute("aria-selected", isMatch ? "true" : "false");
        });

        tabPanels.forEach((panel) => {
            const isMatch = panel.id === `tab-${tabName}`;
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
            const targetTab = button.dataset.tab;
            if (targetTab) {
                activateTab(targetTab, true);
            }
        });
    });
}

