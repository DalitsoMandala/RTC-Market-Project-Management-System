@section('title')
    Users
@endsection

<div>
    <div class="container-fluid">
        <div class="row">
            <x-breadcrumbs />

            <div class="col-12">
                <div x-data="modalData">
                    <x-alerts />

                    {{-- ============================ Users table ============================ --}}
                    <x-table-card title="Users List" subtitle="Manage system users" :isResponsive="false">
                        <x-slot:actions>
                            <x-button type="button" class="btn-sm" @click="handleShowAddUserModal()">
                                <i class="bx bx-plus me-1"></i>
                                Add User(s)
                            </x-button>
                            <x-button type="button" class="btn-sm" data-bs-toggle="modal"
                                data-bs-target="#importUsersModal">
                                <i class="bx bx-upload me-1"></i>
                                Import Users
                            </x-button>
                        </x-slot:actions>

                        <livewire:admin.user-table />
                    </x-table-card>

                    {{-- ============================ Send email ============================ --}}
                    <x-table-card title="Send Email" subtitle="Compose a message to users" :isResponsive="false"
                        :isContent="true">
                        <form wire:submit="sendEmails" x-data="composer" class="ol-compose"
                            @click.outside="closePeopleMenu()">

                            {{-- Top bar --}}
                            <div class="ol-topbar">
                                <button type="submit" class="btn btn-warning" wire:loading.attr="disabled"
                                    wire:target="sendEmails">
                                    <i class="bx bx-send"></i>
                                    <span wire:loading.remove wire:target="sendEmails">Send</span>
                                    <span wire:loading wire:target="sendEmails">Sending...</span>
                                </button>
                                <span class="ol-count"
                                    x-text="recipients.length + (recipients.length === 1 ? ' recipient' : ' recipients')"></span>
                                <a href="#" class="ms-auto small text-danger" x-show="recipients.length" x-cloak
                                    @click.prevent="handleClearAll()">Remove all</a>
                            </div>

                            {{-- To --}}
                            <div class="ol-row">
                                <div class="ol-label">To</div>
                                <div class="ol-to" @click="$refs.toInput.focus()">
                                    <template x-for="recipient in recipients" :key="recipient.id">
                                        <span class="ol-chip" :title="recipient.email">
                                            <span class="ol-avatar" :style="getAvatarStyle(recipient)"
                                                x-text="getInitials(recipient)"></span>
                                            <span class="nm" x-text="recipient.name"></span>
                                            <button type="button" @click.stop="handleRemoveUser(recipient.id)"
                                                aria-label="Remove">&times;</button>
                                        </span>
                                    </template>

                                    <input type="text" class="ol-input" x-ref="toInput" x-model="toQuery"
                                        placeholder="Type @ or a name to add people" autocomplete="off"
                                        @input="handleRecipientSearch()" @focus="handleRecipientSearch()"
                                        @keydown="handleMenuKeydown($event)" @keydown.enter.prevent
                                        @keydown.backspace="handleBackspace()">
                                </div>
                            </div>
                            @error('recipientIds')
                                <div class="px-3 pt-1"><x-error>{{ $message }}</x-error></div>
                            @enderror

                            {{-- Quick add by role --}}
                            <div class="ol-roles">
                                <span>Add everyone in:</span>
                                <template x-for="role in availableRoles" :key="role">
                                    <button type="button" class="btn btn-sm py-0 px-2"
                                        :class="isRoleSelected(role) ? 'btn-primary' : 'btn-outline-primary'"
                                        @click="toggleRole(role)" x-text="formatRole(role)"></button>
                                </template>
                            </div>

                            {{-- Subject --}}
                            <div class="ol-row">
                                <div class="ol-label">Subject</div>
                                <input type="text" class="ol-subject" wire:model="subject"
                                    placeholder="Add a subject">
                            </div>
                            @error('subject')
                                <div class="px-3 pt-1"><x-error>{{ $message }}</x-error></div>
                            @enderror

                            {{-- Toolbar + editor (Quill owns this DOM, so Livewire must not touch it) --}}
                            <div wire:ignore>
                                <div class="ol-toolbar" x-ref="toolbar">
                                    <span class="ql-formats">
                                        <button class="ql-bold"></button>
                                        <button class="ql-italic"></button>
                                        <button class="ql-underline"></button>
                                    </span>
                                    <span class="ql-formats">
                                        <button class="ql-list" value="ordered"></button>
                                        <button class="ql-list" value="bullet"></button>
                                    </span>
                                    <span class="ql-formats">
                                        <button class="ql-link"></button>
                                        <button class="ql-blockquote"></button>
                                        <button class="ql-code-block"></button>
                                    </span>
                                </div>
                                <div x-ref="editor" class="ol-editor"></div>
                            </div>
                            @error('message')
                                <div class="px-3 py-1"><x-error>{{ $message }}</x-error></div>
                            @enderror

                            <div class="ol-hint">
                                Please note that salutations are automatically added to the message. Do not include them
                                in the message body.
                            </div>

                            {{-- Shared @ popup (used by the To field and the message body) --}}
                            <div class="ol-menu" x-show="menuOpen" x-cloak x-transition.opacity.duration.100ms
                                :style="`top:${menuPos.top}px; left:${menuPos.left}px`">
                                <div class="ol-menu-head"
                                    x-text="menuQuery ? 'People matching ' + menuQuery : 'Suggested people'"></div>

                                <template x-for="(person, index) in matches" :key="person.id">
                                    <div class="ol-item" :class="{ active: index === activeIndex }"
                                        @mousedown.prevent="handleSelectPerson(person)"
                                        @mouseenter="activeIndex = index">
                                        <span class="ol-avatar" :style="getAvatarStyle(person)"
                                            x-text="getInitials(person)"></span>
                                        <div class="ol-meta">
                                            <div class="ol-name" x-text="person.name"></div>
                                            <div class="ol-sub"
                                                x-text="person.email + (person.organisation ? ' · ' + person.organisation : '')">
                                            </div>
                                        </div>
                                        <span class="ol-role" x-text="person.role"></span>
                                    </div>
                                </template>

                                <div class="ol-empty" x-show="!matches.length">No people found</div>
                            </div>
                        </form>
                    </x-table-card>
                </div>
            </div>
        </div>
    </div>

    {{-- ============================ Add / edit user modal ============================ --}}
    <div wire:ignore.self class="modal fade" id="addUserModal" tabindex="-1" aria-labelledby="addUserModalLabel"
        aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="addUserModalLabel">{{ $rowId ? 'Edit User' : 'Add New User' }}</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form wire:submit="save">
                        <div class="mb-3">
                            <label for="name" class="form-label">Name</label>
                            <input type="text" class="form-control @error('name') is-invalid @enderror"
                                id="name" wire:model="name">
                            @error('name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="email" class="form-label">Email</label>
                            <input type="email" class="form-control @error('email') is-invalid @enderror"
                                id="email" wire:model="email">
                            @error('email')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="phone" class="form-label">Phone Number</label>
                            <input type="tel" class="form-control @error('phone') is-invalid @enderror"
                                id="phone" wire:model="phone">
                            @error('phone')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        {{-- Role + organisation (organisation list depends on the role) --}}
                        <div x-data="organisationData">
                            <div class="mb-3">
                                <label for="role" class="form-label">Role</label>
                                <select id="role" class="form-select @error('role') is-invalid @enderror"
                                    x-model="role" @change="handleRoleChange($event)">
                                    <option value="">Select a role</option>
                                    <template x-for="roleName in roles" :key="roleName">
                                        <option :value="roleName" :selected="roleName === role"
                                            x-text="formatRole(roleName)"></option>
                                    </template>
                                </select>
                                @error('role')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="mb-3">
                                <label for="organisation" class="form-label">Organisation</label>
                                <select id="organisation"
                                    class="form-select @error('organisation') is-invalid @enderror"
                                    x-model="organisation">
                                    <option value="">Select an organisation</option>
                                    <template x-for="record in organisations" :key="record.id">
                                        <option :value="record.id"
                                            :selected="String(record.id) === String(organisation)"
                                            x-text="record.name"></option>
                                    </template>
                                </select>
                                @error('organisation')
                                    <x-error>{{ $message }}</x-error>
                                @enderror
                            </div>
                        </div>

                        {{-- Password (when editing, it is locked until "Change password" is clicked) --}}
                        <div x-data="passwordData">
                            <div class="mb-3">
                                <div class="d-flex justify-content-between">
                                    <label for="password" class="form-label">Password</label>
                                    <span x-show="edit" x-cloak>
                                        <a href="#" x-show="!changePassword"
                                            @click.prevent="changePassword = true">Change password</a>
                                        <a href="#" x-show="changePassword" class="text-danger"
                                            @click.prevent="changePassword = false">Cancel</a>
                                    </span>
                                </div>
                                <input type="password" class="form-control @error('password') is-invalid @enderror"
                                    id="password" wire:model="password" :disabled="!changePassword"
                                    :class="{ 'bg-light': !changePassword }">
                                @error('password')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="mb-3">
                                <label for="password_confirmation" class="form-label">Confirm Password</label>
                                <input type="password" class="form-control" id="password_confirmation"
                                    wire:model="password_confirmation" :disabled="!changePassword"
                                    :class="{ 'bg-light': !changePassword }">
                            </div>
                        </div>

                        <hr>

                        <div class="d-flex justify-content-center gap-1">
                            <x-button type="button" :variant="'secondary'" :padding="true"
                                data-bs-dismiss="modal">Close</x-button>
                            <x-button type="submit" icon="fas fa-save" target="save" :padding="true">
                                {{ $rowId ? 'Update' : 'Submit' }}
                            </x-button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    {{-- ============================ Import users modal ============================ --}}
    <div wire:ignore.self class="modal fade" id="importUsersModal" tabindex="-1"
        aria-labelledby="importUsersModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="importUsersModalLabel">Import Users</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body" x-data="uploadUsers">
                    <form @submit.prevent="handleUploadData()">
                        <div class="mb-3">
                            <x-button type="button" @click="handleDownloadTemplate()"
                                class="btn btn-success btn-sm">Download Template</x-button>
                        </div>

                        <hr>

                        <div class="mb-3">
                            <label for="fileInput" class="form-label">Upload Excel File</label>
                            <input class="form-control" type="file" id="fileInput" x-ref="fileInput"
                                accept=".xlsx, .xls">
                        </div>
                        <hr>
                        <div class="d-flex justify-content-center gap-1">
                            <x-button type="button" :variant="'secondary'" :padding="true"
                                data-bs-dismiss="modal">Close</x-button>
                            <x-button type="submit" :variant="'warning'" :padding="true">Upload data</x-button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    {{-- ============================ Delete / restore modals ============================ --}}
    <x-modal id="view-delete-modal" title="Delete record">
        <form wire:submit="deleteUser">
            <p class="mb-0 text-center">Are you sure you want to delete this record?</p>
            <p class="text-center">ID: {{ $rowId }}</p>
            <div class="gap-1 mt-3 d-flex justify-content-center">
                <x-button type="button" size="md" variant="secondary" class="px-5" data-bs-dismiss="modal">
                    No
                </x-button>
                <x-button type="submit" size="md" variant="theme-red" class="px-5">
                    Yes
                </x-button>
            </div>
        </form>
    </x-modal>

    <x-modal id="view-restore-modal" title="Restore record">
        <form wire:submit="restoreUser">
            <p class="mb-0 text-center">Are you sure you want to restore this record?</p>
            <p class="text-center">ID: {{ $rowId }}</p>
            <div class="gap-1 mt-3 d-flex justify-content-center">
                <x-button type="button" size="md" variant="secondary" class="px-5" data-bs-dismiss="modal">
                    No
                </x-button>
                <x-button type="submit" size="md" variant="success" class="px-5">
                    Yes
                </x-button>
            </div>
        </form>
    </x-modal>
</div>

{{-- @assets loads exactly once and survives wire:navigate --}}
@assets
    <script src="https://cdnjs.cloudflare.com/ajax/libs/xlsx/0.18.5/xlsx.full.min.js"></script>
    <link href="https://cdn.jsdelivr.net/npm/quill@2.0.3/dist/quill.snow.css" rel="stylesheet" />
    <script src="https://cdn.jsdelivr.net/npm/quill@2.0.3/dist/quill.js"></script>
@endassets


@script
    <script>
        // "project-manager" -> "Project Manager"
        formatRole = (role) => // "project-manager" -> "Project Manager"
            (role || '').replace(/-/g, ' ').replace(/\b\w/g, (letter) => letter.toUpperCase());

        openModal = (id) =>
            bootstrap.Modal.getOrCreateInstance(document.getElementById(id)).show();

        // The table asks us to open a modal: { name: '<modal element id>' }
        ['edit', 'delete', 'restore'].forEach((action) => {
            $wire.on(`showModal-${action}`, ({
                name
            }) => openModal(name));
        });

        // After any change, close whatever modal is open
        $wire.on('refresh', () => {
            document.querySelectorAll('.modal.show').forEach((element) => {
                bootstrap.Modal.getInstance(element)?.hide();
            });
        });

        $wire.on('edit', () => window.scrollTo({
            top: 0,
            behavior: 'smooth'
        }));



        Alpine.data('modalData', () => ({
            handleShowAddUserModal() {
                $wire.resetForm();
                openModal('addUserModal');
            },
        }));

        // ---------- Import users from Excel ----------
        Alpine.data('uploadUsers', () => ({
            handleDownloadTemplate() {
                const worksheet = XLSX.utils.aoa_to_sheet([
                    ['email', 'name', 'organisation', 'role']
                ]);
                const workbook = XLSX.utils.book_new();
                XLSX.utils.book_append_sheet(workbook, worksheet, 'Users');
                XLSX.writeFile(workbook, 'user_template.xlsx');
            },

            async handleUploadData() {
                const fileInput = this.$refs.fileInput;
                const file = fileInput.files[0];

                if (!file) {
                    $wire.noFile();
                    return;
                }

                const workbook = XLSX.read(await file.arrayBuffer(), {
                    type: 'array'
                });
                const firstSheet = workbook.Sheets[workbook.SheetNames[0]];
                const rows = XLSX.utils.sheet_to_json(firstSheet, {
                    defval: null
                });

                await $wire.importUsers(
                    rows.map(({
                        email,
                        name,
                        organisation,
                        role
                    }) => ({
                        email,
                        name,
                        organisation,
                        role
                    }))
                );

                fileInput.value = '';
            },
        }));

        // ---------- Role + organisation selects ----------
        Alpine.data('organisationData', () => ({
            role: $wire.entangle('role'),
            organisation: $wire.entangle('organisation'),
            formatRole,

            get roles() {
                return $wire.roles || [];
            },

            // Business rule: "external" users belong to any organisation except CIP, everyone else to CIP
            organisationsFor(role) {
                const all = $wire.organisations || [];
                if (!role) return all;
                return all.filter((record) => (role === 'external' ? record.name !== 'CIP' : record.name ===
                    'CIP'));
            },

            get organisations() {
                return this.organisationsFor(this.role);
            },

            // Only runs when the person changes the role, not when the server fills the form for editing
            handleRoleChange(event) {
                const stillValid = this.organisationsFor(event.target.value)
                    .some((record) => String(record.id) === String(this.organisation));
                if (!stillValid) this.organisation = '';
            },
        }));

        Alpine.data('passwordData', () => ({
            changePassword: $wire.entangle('changePassword'),
            edit: $wire.entangle('rowId'),
        }));

        // ---------- Email composer ----------
        Alpine.data('composer', () => {
            // Keep Quill OUT of Alpine's reactive proxy
            let quill = null;

            return {
                recipientIds: $wire.entangle('recipientIds'),
                formatRole,

                toQuery: '',
                menuOpen: false,
                menuQuery: '',
                menuPos: {
                    top: 0,
                    left: 0
                },
                activeIndex: 0,
                mention: null, // { index, length } of the "@text" being typed in the editor

                init() {
                    quill = new Quill(this.$refs.editor, {
                        theme: 'snow',
                        placeholder: 'Type your message...',
                        modules: {
                            toolbar: this.$refs.toolbar
                        },
                    });

                    quill.on('text-change', () => {
                        const isEmpty = quill.getText().trim().length === 0;
                        // 3rd arg false = don't send a network request on every keystroke
                        $wire.set('message', isEmpty ? null : quill.root.innerHTML, false);
                    });

                    $wire.on('emails-sent', () => {
                        quill.setText('');
                        this.toQuery = '';
                    });
                },
                // ---------- data ----------
                get allUsers() {
                    return $wire.allUsers || [];
                },

                get recipients() {
                    const usersById = new Map(this.allUsers.map((user) => [user.id, user]));
                    return this.recipientIds.map((id) => usersById.get(id)).filter(Boolean);
                },

                get availableRoles() {
                    return ($wire.roles || [])
                        .filter((role) => this.allUsers.some((user) => user.role === role));
                },

                // ---------- recipients ----------
                handleAddUser(user) {
                    if (!this.recipientIds.includes(user.id)) {
                        this.recipientIds = [...this.recipientIds, user.id];
                    }
                },

                handleRemoveUser(userId) {
                    this.recipientIds = this.recipientIds.filter((id) => id !== userId);
                },

                handleClearAll() {
                    this.recipientIds = [];
                },

                handleBackspace() {
                    // Backspace in an empty input removes the last recipient
                    if (this.toQuery || !this.recipientIds.length) return;
                    this.recipientIds = this.recipientIds.slice(0, -1);
                },

                // ---------- "add everyone in role" buttons ----------
                idsInRole(role) {
                    return this.allUsers.filter((user) => user.role === role).map((user) => user.id);
                },

                isRoleSelected(role) {
                    const ids = this.idsInRole(role);
                    return ids.length > 0 && ids.every((id) => this.recipientIds.includes(id));
                },

                toggleRole(role) {
                    const ids = this.idsInRole(role);
                    this.recipientIds = this.isRoleSelected(role) ?
                        this.recipientIds.filter((id) => !ids.includes(id)) : [...new Set([...this.recipientIds, ...
                            ids
                        ])];
                },

                // ---------- popup ----------
                get matches() {
                    const search = this.menuQuery.toLowerCase();

                    return this.allUsers
                        .filter((user) => !this.recipientIds.includes(user.id))
                        .filter((user) =>
                            `${user.name} ${user.email} ${user.organisation ?? ''}`.toLowerCase().includes(
                                search)
                        )
                        .slice(0, 8);
                },

                openPeopleMenu(query) {
                    if (query !== this.menuQuery) this.activeIndex = 0;
                    this.menuQuery = query;
                    this.menuOpen = true;
                },

                closePeopleMenu() {
                    this.menuOpen = false;
                    this.menuQuery = '';
                },

                positionPeopleMenu(anchorRect) {
                    const rootRect = this.$root.getBoundingClientRect();
                    const menuWidth = 340;
                    const left = anchorRect.left - rootRect.left;

                    this.menuPos = {
                        top: anchorRect.bottom - rootRect.top + 4,
                        left: Math.max(0, Math.min(left, rootRect.width - menuWidth - 8)),
                    };
                },

                handleRecipientSearch() {
                    const query = this.toQuery.trim();
                    if (!query) {
                        this.closePeopleMenu();
                        return;
                    }
                    this.openPeopleMenu(query.replace(/^@/, ''));
                    this.positionPeopleMenu(this.$refs.toInput.getBoundingClientRect());
                },

                // Show the popup when the caret is right after "@something" in the editor

                // ---------- keyboard ----------
                // Returns true when the key was used by the popup
                handleMenuKeydown(event) {
                    if (!this.menuOpen) return false;

                    const count = this.matches.length;

                    if (event.key === 'Escape') {
                        event.preventDefault();
                        this.closePeopleMenu();
                        return true;
                    }

                    if (!count) return false;

                    if (event.key === 'ArrowDown') {
                        this.activeIndex = (this.activeIndex + 1) % count;
                    } else if (event.key === 'ArrowUp') {
                        this.activeIndex = (this.activeIndex - 1 + count) % count;
                    } else if (event.key === 'Enter' || event.key === 'Tab') {
                        this.handleSelectPerson(this.matches[this.activeIndex]);
                    } else {
                        return false;
                    }

                    event.preventDefault();
                    return true;
                },

                // ---------- select ----------
                handleSelectPerson(person) {
                    this.handleAddUser(person);
                    this.toQuery = '';
                    this.closePeopleMenu();
                    this.$nextTick(() => this.$refs.toInput?.focus());
                },

                // ---------- avatars ----------
                getInitials(user) {
                    return (user.name || '?')
                        .split(/\s+/)
                        .slice(0, 2)
                        .map((word) => word[0]?.toUpperCase())
                        .join('');
                },

                getAvatarStyle(user) {
                    let hash = 0;
                    for (const character of user.name || '') {
                        hash = (hash * 31 + character.charCodeAt(0)) % 360;
                    }
                    return `background:hsl(${hash}, 55%, 45%)`;
                },
            };
        });
    </script>
@endscript
