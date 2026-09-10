import { NextFunction, Request, RequestHandler, Response } from "express";

export default function response(): RequestHandler {
    return (req: Request, res: Response, next: NextFunction) => {
        (res as any).respond = (data: any): Response => res.json({
            status: res.statusCode,
            payload: data
        });
        next();
    };
}
