export type UserRole = 'superadmin' | 'admin';

export type User = {
    id: number;
    name: string;
    username: string;
    role: UserRole;
    email: string;
    avatar?: string;
    email_verified_at: string | null;
    created_at: string;
    updated_at: string;
    [key: string]: unknown;
};

export type Auth = {
    user: User | null;
};
