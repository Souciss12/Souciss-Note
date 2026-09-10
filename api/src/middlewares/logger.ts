import { NextFunction, Request, RequestHandler, Response } from "express";
import { Logger } from "../utils/logger";

export default function logger(): RequestHandler {
    return (req: Request, res: Response, next: NextFunction) => {
        (req as any).logger = new Logger();

        const start = new Date();
        res.on("finish", () => {
            const timestamp = start.toLocaleString();
            const duration = new Date().getTime() - start.getTime();
            const user = (req as any).user?.id ?? "unknown";
            const dump = (req as any).logger.dump();
            console.log(`[${timestamp}] ${res.statusCode} ${req.method} ${req.originalUrl} (${duration}ms) ip=${req.ip} user=${user} ${dump}`);
        });

        next();
    };
}
