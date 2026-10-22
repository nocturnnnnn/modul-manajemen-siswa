export interface PaginationLink {
    url: string | null;
    label: string;
    active: boolean;
}

export interface PaginatedData<T> {
    data: T[];
    current_page: number;
    first_page_url: string;
    from: number | null;
    last_page: number;
    last_page_url: string;
    links: PaginationLink[];
    next_page_url: string | null;
    path: string;
    per_page: number;
    prev_page_url: string | null;
    to: number | null;
    total: number;
}

export interface SelectOption<T = string | number> {
    label: string;
    value: T;
    disabled?: boolean;
}

export type NotificationType = 'success' | 'error' | 'warning' | 'info';

export interface NotificationToast {
    id: string;
    type: NotificationType;
    message: string;
    title?: string;
    timeout?: number;
}

export interface BreadcrumbItem {
    label: string;
    href?: string;
    current?: boolean;
}

export interface FilterParams {
    search?: string;
    status?: string;
    kelas?: string | number;
    tahun_ajaran?: string | number;
    jenis?: string | number;
    page?: number;
    per_page?: number;
    sort_by?: string;
    sort_dir?: 'asc' | 'desc';
}
