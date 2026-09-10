import chalk from "chalk";

export class Logger {
    private logs: string[] = [];

    public log(message: string, level: "info" | "warn" | "error" = "info") {
        const color = level === "error"
            ? chalk.red : level === "warn"
                ? chalk.yellow : chalk.gray;
        this.logs.push(color(`[${level.toUpperCase()}] ${message}`));
    }

    public dump(): string {
        if (this.logs.length > 0) {
            return `\n ↳ ${this.logs.join("\n ↳ ")}`;
        } else {
            return ""
        }
    }
}
