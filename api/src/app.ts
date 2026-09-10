import chalk from "chalk";
import http from "http";
import { exit } from "process";
import app from "./express";
import { Config } from "./utils/config/config";
import { Database } from "./utils/database/database";

const server = http.createServer(app);

const host = Config.api.host ?? "0.0.0.0";
const port = Config.api.port ?? 5000;

server.listen(port, host, async () => {
    const db = await Database.getInstance();
    const isConnected = await db.isConnected();
    logStartupInfo(host, port, process.env.NODE_ENV ?? "development", isConnected);
    if (!isConnected) {
        console.log(chalk.red("  Failed to connect to the database."));
        console.log(chalk.red("  Please check your configuration."));
        console.log(chalk.gray("──────────────────────────────────────────────"));
        exit(1);
    }
});


function logStartupInfo(host: string, port: number, env: string, dbStatus: boolean) {
    const now = new Date().toLocaleString();
    const uptime = (process.uptime() * 1000).toFixed(0);
    
    console.log(chalk.gray("──────────────────────────────────────────────"));
    console.log(`${chalk.magenta("  Souciss Note API")} ${chalk.gray("v2.0.0")}`);
    console.log(chalk.gray("──────────────────────────────────────────────"));
    console.log(`${chalk.gray.bold("  Environment")} : ${chalk.yellow(env)}`);
    console.log(`${chalk.gray.bold("  Listening")}   : ${chalk.cyan(`http://${host}:${port}`)}`);
    console.log(`${chalk.gray.bold("  Database")}    : ${dbStatus ? chalk.green("Connected") : chalk.red("Not Connected")}`);
    console.log(`${chalk.gray.bold("  Started at")}  : ${chalk.gray(now)}`);
    console.log(`${chalk.gray.bold("  Uptime")}      : ${chalk.gray(uptime + " ms")}`);
    console.log(chalk.gray("──────────────────────────────────────────────"));
}
