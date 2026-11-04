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

export function initEmployeOrdersFilter() {
    const statusSelect = document.getElementById("employe-order-status-filter");
    const clientInput = document.getElementById("employe-order-client-filter");
    const orderItems = document.querySelectorAll(".employe-order-item");

    if (!statusSelect || !clientInput || orderItems.length === 0) {
        return;
    }

    const filterOrders = () => {
        const selectedStatus = statusSelect.value;
        const clientQuery = clientInput.value.trim().toLowerCase();

        orderItems.forEach((item) => {
            const itemStatus = item.dataset.orderStatus || "";
            const itemClient = item.dataset.orderClient || "";

            const matchesStatus =
                selectedStatus === "all" || itemStatus === selectedStatus;
            const matchesClient =
                clientQuery === "" || itemClient.includes(clientQuery);

            item.style.display =
                matchesStatus && matchesClient ? "flex" : "none";
        });
    };

    statusSelect.addEventListener("change", filterOrders);
    clientInput.addEventListener("input", filterOrders);
}

export function initEmployeOrderCancelToggle() {
    const toggleButtons = document.querySelectorAll(
        ".js-toggle-cancel, .js-toggle-box"
    );

    if (toggleButtons.length === 0) {
        return;
    }

    toggleButtons.forEach((button) => {
        button.addEventListener("click", () => {
            const targetId = button.dataset.target;
            if (!targetId) {
                return;
            }

            const targetBox = document.getElementById(targetId);
            if (targetBox) {
                targetBox.hidden = !targetBox.hidden;
            }
        });
    });
}

export function initEmployeDeleteModal() {
    const deleteForms = document.querySelectorAll(".js-delete-form");
    const modal = document.getElementById("employe-confirm-modal");
    const messageEl = document.getElementById("employe-confirm-message");
    const cancelBtn = document.getElementById("employe-confirm-cancel");
    const submitBtn = document.getElementById("employe-confirm-submit");

    if (
        deleteForms.length === 0 ||
        !modal ||
        !messageEl ||
        !cancelBtn ||
        !submitBtn
    ) {
        return;
    }

    let pendingForm = null;

    const closeModal = () => {
        modal.hidden = true;
        pendingForm = null;
    };

    deleteForms.forEach((form) => {
        form.addEventListener("submit", (event) => {
            if (form.dataset.confirmed === "true") {
                return;
            }

            event.preventDefault();
            pendingForm = form;
            messageEl.textContent =
                form.dataset.confirmMessage ||
                "Êtes-vous sûr de vouloir supprimer cet élément ?";
            modal.hidden = false;
        });
    });

    cancelBtn.addEventListener("click", closeModal);

    modal.addEventListener("click", (event) => {
        if (event.target === modal) {
            closeModal();
        }
    });

    document.addEventListener("keydown", (event) => {
        if (event.key === "Escape" && !modal.hidden) {
            closeModal();
        }
    });

    submitBtn.addEventListener("click", () => {
        if (pendingForm) {
            pendingForm.dataset.confirmed = "true";
            pendingForm.submit();
        }
    });
}
