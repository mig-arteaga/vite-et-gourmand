// Gestion des onglets et filtres de l'espace employé
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

export function initEmployeAvisFilter() {
    const filterButtons = document.querySelectorAll("[data-avis-filter]");
    const avisItems = document.querySelectorAll(".employe-avis-item");

    if (filterButtons.length === 0 || avisItems.length === 0) {
        return;
    }

    filterButtons.forEach((button) => {
        button.addEventListener("click", () => {
            const selectedStatus = button.dataset.avisFilter;

            filterButtons.forEach((btn) => {
                btn.classList.toggle("active", btn === button);
            });

            avisItems.forEach((item) => {
                const itemStatus = item.dataset.avisStatus;
                const shouldShow =
                    selectedStatus === "all" || itemStatus === selectedStatus;
                item.style.display = shouldShow ? "flex" : "none";
            });
        });
    });
}
