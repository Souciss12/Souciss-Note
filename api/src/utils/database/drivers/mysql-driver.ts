import mysql, { Pool, RowDataPacket } from "mysql2/promise";
import { Config } from "../../config/config";
import { DatabaseInstance } from "../database-instance";

export class MySQLDriver extends DatabaseInstance {
    private pool!: Pool;

    async _connect(): Promise<void> {
        try {
            this.pool = mysql.createPool({
                host: Config.database.host ?? "localhost",
                port: Config.database.port ?? 3306,
                user: Config.database.user ?? "root",
                password: Config.database.password,
                database: Config.database.database,
                waitForConnections: true,
                connectionLimit: 10,
                queueLimit: 0,
            });
        } catch (error: any) {
            this.handleError(error);
        }
    }

    public async isConnected(): Promise<boolean> {
        try {
            const conn = await this.pool.getConnection();
            await conn.query("SELECT 1");
            conn.release();
            return true;
        } catch (error: any) {
            return false;
        }
    }

    public async execute(sql: string, params: any[] = []): Promise<void> {
        try {
            await this.pool.execute(sql, params);
        } catch (error: any) {
            this.handleError(error);
        }
    }

    public async fetchOne<T = any>(sql: string, params: any[] = []): Promise<T | null> {
        try {
            const [rows] = await this.pool.execute(sql, params);
            if (Array.isArray(rows)) {
                return ((rows as RowDataPacket[])[0] as T) ?? null;
            }
            return null;
        } catch (error: any) {
            this.handleError(error);
        }
    }

    public async fetchAll<T = any>(sql: string, params: any[] = []): Promise<T[]> {
        try {
            const [rows] = await this.pool.execute(sql, params);
            if (Array.isArray(rows)) {
                return rows as T[];
            }
            return [];
        } catch (error: any) {
            this.handleError(error);
        }
    }
}
