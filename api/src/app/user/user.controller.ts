import { DatabaseInstance } from "../../utils/database/database-instance";
import { User } from "./user";

export class UserController {

    private COLUMNS = `
    Users.user_id,
    Users.email,
    Users.username,
    Users.created_at,
    Users.updated_at
    `;

    public constructor(private readonly db: DatabaseInstance) { }

    public async findUserById(id: number): Promise<User | null> {
        const user = await this.db.fetchOne<User>(`SELECT ${this.COLUMNS} FROM Users WHERE user_id = ?`, [id]);
        return this.parse(user);
    }

    public async findUserByLoginAndPassword(login: string, password: string): Promise<User | null> {
        const user = await this.db.fetchOne<User>(`SELECT ${this.COLUMNS} FROM Users WHERE (username = ? OR email = ?) AND password = ?`, [login, login, password]);
        return this.parse(user);
    }

    private parse(row: any): User | null {
        if (row === null) return null;
        return {
            id: row.user_id,
            email: row.email,
            username: row.username,
            createdAt: new Date(row.created_at),
            updatedAt: new Date(row.updated_at)
        };
    }

    private parseAll(rows: any[]): User[] {
        return rows.map(row => this.parse(row)!);
    }
}
