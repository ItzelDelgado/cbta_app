<aside id="logo-sidebar"
    class="fixed top-0 left-0 z-40 w-44 h-screen pt-20 transition-transform -translate-x-full bg-white border-r border-gray-200 sm:translate-x-0 dark:bg-gray-800 dark:border-gray-700"
    x-data="{ openMenu: null }" aria-label="Sidebar">

    <div class="h-full px-3 pb-4 overflow-y-auto bg-white dark:bg-gray-800">
        <ul class="space-y-2 font-medium">

            <!-- Nutricionales -->
            <li>
                <button @click="openMenu === 'nutricionales' ? openMenu = null : openMenu = 'nutricionales'"
                    class="flex w-full items-center p-2 text-gray-900 rounded-lg dark:text-white hover:bg-gray-100 dark:hover:bg-gray-700">
                    <i class="fas fa-vial text-gray-500"></i>
                    <span class="ms-3">Nutricionales</span>
                </button>
                <ul x-show="openMenu === 'nutricionales'" class="pl-4 space-y-2">
                    @can('solicitudes_index')
                        <li>
                            <a href="{{ route('admin.nutricionales.solicitudes.index') }}"
                                class="flex items-center p-2 text-gray-900 rounded-lg dark:text-white hover:bg-gray-100 dark:hover:bg-gray-700 group {{ request()->routeIs('admin.nutricionales.solicitudes.*') ? 'bg-gray-100' : '' }}">
                                <i class="fa-solid fa-file-import text-gray-500"></i>
                                <span class="ms-3">Solicitudes</span>
                            </a>
                        </li>
                    @endcan
                    @can('medicamentos_nutricionales')
                        <li>
                            <a href="{{ route('admin.nutricionales.medicines.index') }}"
                                class="flex items-center p-2 text-gray-900 rounded-lg dark:text-white hover:bg-gray-100 dark:hover:bg-gray-700 group {{ request()->routeIs('admin.nutricionales.solicitudes.*') ? 'bg-gray-100' : '' }}">
                                <i class="fa-solid fa-file-import text-gray-500"></i>
                                <span class="ms-3">Medicamentos</span>
                            </a>
                        </li>
                    @endcan
                </ul>
            </li>

            <!-- Oncológicas -->
            <li>
                <button @click="openMenu === 'oncologicas' ? openMenu = null : openMenu = 'oncologicas'"
                    class="flex w-full items-center p-2 text-gray-900 rounded-lg dark:text-white hover:bg-gray-100 dark:hover:bg-gray-700">
                    <i class="fas fa-vial text-gray-500"></i>
                    <span class="ms-3">Oncológicas</span>
                </button>

                <ul x-show="openMenu === 'oncologicas'" class="pl-4 space-y-2">
                    @can('solicitudes_index')
                        <li>
                            <a href="{{ route('admin.oncologicos.solicitudes.index') }}"
                                class="flex items-center p-2 text-gray-900 rounded-lg dark:text-white hover:bg-gray-100 dark:hover:bg-gray-700 group {{ request()->routeIs('admin.nutricionales.solicitudes.*') ? 'bg-gray-100' : '' }}">
                                <i class="fa-solid fa-file-import text-gray-500"></i>
                                <span class="ms-3">Solicitudes</span>
                            </a>
                        </li>
                    @endcan
                    @can('medicamentos_oncologicos')
                        <li>
                            <a href="{{ route('admin.oncologicos.medicines.catalog.index') }}"
                                class="flex items-center p-2 text-gray-900 rounded-lg dark:text-white hover:bg-gray-100 dark:hover:bg-gray-700 group {{ request()->routeIs('admin.nutricionales.solicitudes.*') ? 'bg-gray-100' : '' }}">
                                <i class="fa-solid fa-file-import text-gray-500"></i>
                                <span class="ms-3">Medicamentos</span>
                            </a>
                        </li>
                    @endcan
                    @can('medicamentos_oncologicos')
                        <li>
                            <a href="{{ route('admin.oncologicos.medicines.index') }}"
                                class="flex items-center p-2 text-gray-900 rounded-lg dark:text-white hover:bg-gray-100 dark:hover:bg-gray-700 group {{ request()->routeIs('admin.nutricionales.solicitudes.*') ? 'bg-gray-100' : '' }}">
                                <i class="fa-solid fa-file-import text-gray-500"></i>
                                <span class="ms-3">Listas</span>
                            </a>
                        </li>
                    @endcan

                </ul>
            </li>

            <!-- Hospitales -->
            @can('hospitales')
                <li>
                    <a href="{{ route('admin.hospitals.index') }}"
                        class="flex items-center p-2 text-gray-900 rounded-lg dark:text-white hover:bg-gray-100 dark:hover:bg-gray-700 group {{ request()->routeIs('admin.hospitals.*') ? 'bg-gray-100' : '' }}">
                        <i class="fa-solid fa-hospital text-gray-500"></i>
                        <span class="ms-3">Hospitales</span>
                    </a>
                </li>
            @endcan

            <!-- Usuarios -->
            @can('usuarios')
                <li>
                    <a href="{{ route('admin.users.index') }}"
                        class="flex items-center p-2 text-gray-900 rounded-lg dark:text-white hover:bg-gray-100 dark:hover:bg-gray-700 group {{ request()->routeIs('admin.users.*') ? 'bg-gray-100' : '' }}">
                        <i class="fa-solid fa-users text-gray-500"></i>
                        <span class="ms-3">Usuarios</span>
                    </a>
                </li>
            @endcan

            <!-- Roles -->
            @can('roles')
                <li>
                    <a href="{{ route('admin.roles.index') }}"
                        class="flex items-center p-2 text-gray-900 rounded-lg dark:text-white hover:bg-gray-100 dark:hover:bg-gray-700 group {{ request()->routeIs('admin.roles.*') ? 'bg-gray-100' : '' }}">
                        <i class="fa-solid fa-user-tag text-gray-500"></i>
                        <span class="ms-3">Roles</span>
                    </a>
                </li>
            @endcan

            <!-- Permisos -->
            @can('permisos')
                <li>
                    <a href="{{ route('admin.permissions.index') }}"
                        class="flex items-center p-2 text-gray-900 rounded-lg dark:text-white hover:bg-gray-100 dark:hover:bg-gray-700 group {{ request()->routeIs('admin.permissions.*') ? 'bg-gray-100' : '' }}">
                        <i class="fa-solid fa-key text-gray-500"></i>
                        <span class="ms-3">Permisos</span>
                    </a>
                </li>
            @endcan
        </ul>
    </div>
</aside>
