export default function draftObject(userId, formName) {
    return {
        form: {},
        showInfo: false,
        userId: userId,
        formName: formName,
        draftData: {},
        draftName: () => {
            return "formDraft" + this.formName + "-" + this.userId;
        },
        extractNestedData(sourceData, prefix) {
            const indices = [];
            const structuredData = [];

            for (const key in sourceData) {
                if (key.startsWith(`${prefix}.`)) {
                    const [_, index, property] = key.split(".");

                    // Track unique indices
                    if (!indices.includes(index)) {
                        indices.push(index);
                    }

                    // Build structured object
                    if (!structuredData[index]) {
                        structuredData[index] = {};
                    }
                    structuredData[index][property] = sourceData[key];
                }
            }

            // Filter out empty slots (if any) and return
            const filteredData = structuredData.filter(Boolean);
            return {
                count: indices.length,
                data: filteredData,
            };
        },

        saveDraft(event) {
            const input = event.target;
            const modelKey =
                input.getAttribute("wire:model") ||
                input.getAttribute("x-model");
            if (!modelKey) {
                return;
            }

            // Get current value based on input type
            let value;
            if (input.type === "checkbox") {
                // Handle checkbox group
                const checkboxes = document.querySelectorAll(
                    `[wire\\:model="${modelKey}"], [x-model="${modelKey}"]`
                );
                const checkboxValues = [];

                checkboxes.forEach((checkbox) => {
                    if (checkbox.checked) {
                        checkboxValues.push(checkbox.value); // Use 'on' if no value attribute
                    }
                });

                // If it's a single checkbox (not a group), just use boolean
                value = checkboxes.length > 1 ? checkboxValues : input.checked;
            } else if (input.type === "radio") {
                value = input.checked ? input.value : undefined;
            } else {
                value = input.value;
            }

            // Skip if radio button was unchecked
            if (value === undefined) {
                return;
            }

            // Load existing draft first
            const currentDraft =
                JSON.parse(localStorage.getItem(this.draftName())) || {};

            // Update only the changed field
            currentDraft[modelKey] = value;

            // Save back to localStorage
            localStorage.setItem(
                this.draftName(),
                JSON.stringify(currentDraft)
            );

            // Also update local form reference
            this.form = currentDraft;
        },

        clearDrafts() {
            localStorage.removeItem(this.draftName());
        },

        async init() {
            const draft = localStorage.getItem(this.draftName());
            const form = document.getElementById("mainForm");
            if (draft) {
                this.showInfo = true;
                let savedDraft = JSON.parse(draft);
                this.draftData = savedDraft;

                await this.$nextTick();
                // First set all values
                for (const key in savedDraft) {
                    let input = form.querySelector(
                        `[wire\\:model="${key}"], [x-model="${key}"]`
                    );
                    if (input) {
                        // Wait for Alpine to be ready

                        if (input.type === "checkbox") {
                            input.checked = savedDraft[key];
                        } else if (input.type === "radio") {
                            input.checked = input.value == savedDraft[key];
                        } else {
                            input.value = savedDraft[key] || "";
                        }

                        // Force Alpine to recognize the change
                        input._x_model.set(savedDraft[key]);

                        // Trigger Livewire update if needed
                        if (input.getAttribute("wire:model")) {
                            input.dispatchEvent(
                                new Event("input", {
                                    bubbles: true,
                                })
                            );
                        }
                    }
                }
            } else {
                this.showInfo = false;
                this.draftData = {};
            }

            form.addEventListener("input", (event) => this.saveDraft(event));
        },
    };
}
