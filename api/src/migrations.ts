import fs from "fs";
import path from "path";
import { Database } from "./utils/database/database";

async function runMigrations() {
    const db = await Database.getInstance();

    await db.execute(`
    CREATE TABLE IF NOT EXISTS migrations (
        id INT PRIMARY KEY AUTO_INCREMENT,
        name VARCHAR(255) NOT NULL,
        executed_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP
    )
    `);

    const applied = await db.fetchAll<{ name: string }>("SELECT name FROM migrations");
    const appliedNames = applied.map(migration => migration.name);

    const migrationsDir = path.join(process.cwd(), "migrations");
    const files = fs.readdirSync(migrationsDir).filter(file => file.endsWith(".sql")).sort();

    for (const file of files) {
        if (appliedNames.includes(file)) {
            console.log(`⚪ Skipped ${file}`);
            continue;
        }

        try {
            const content = fs.readFileSync(path.join(migrationsDir, file), "utf-8");

            const statements = content.split(";").map(s => s.trim()).filter(Boolean)
            for (const statement of statements) {
                await db.execute(statement);
            }

            await db.execute("INSERT INTO migrations (name) VALUES (?)", [file]);
            console.log(`✅ Applied ${file}`);
        } catch (error: any) {
            console.error(`❌ Failed to apply ${file}: ${error.message}`);
            break;
        }
    }

    console.log("🎉 Migrations complete");
    process.exit();
}

runMigrations();
