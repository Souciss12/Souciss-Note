import { NextFunction, Request, RequestHandler, Response } from "express";
import { Database } from "../utils/database/database";

export default function database(): RequestHandler {
    return async (req: Request, res: Response, next: NextFunction) => {
        try {
            (req as any).db = await Database.getInstance();
            next();
        } catch (error) {
            const err = error as Error;
            next({
                name: err.name,
                message: err.message
            });
        }
    };
}
