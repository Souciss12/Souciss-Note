import { Config } from "../config/config";
import { DatabaseInstance } from "./database-instance";
import { MySQLDriver } from "./drivers/mysql-driver";

export class Database {
    private static instance: DatabaseInstance | null = null;

    public static async getInstance(): Promise<DatabaseInstance> {
        if (this.instance === null) {
            let instance: DatabaseInstance;
            switch (Config.database.driver) {
                case "mysql":
                case "mariadb":
                default:
                    instance = new MySQLDriver();
                    break;
            }
            await instance._connect();
            this.instance = instance;
        }
        return this.instance;
    }
}
