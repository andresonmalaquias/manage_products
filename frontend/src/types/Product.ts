export interface Product {
    id: number;
    name: string;
    description: string | null;
    price: number;
    expiration_date: string;
    image: File | null;
    category_id: number;
}
