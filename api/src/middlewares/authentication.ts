import { NextFunction, Request, RequestHandler, Response } from "express";
import jwt from "jsonwebtoken";
import { UserController } from "../app/user/user.controller";
import { Config } from "../utils/config/config";

export default function authentication(optional: boolean = false): RequestHandler {
    return async (req: Request, res: Response, next: NextFunction) => {
        const token = req.cookies?.jwt;

        if (!token) {
            if (optional) {
                (req as any).user = null;
                return next();
            }
            return next({ status: 401, message: "TOKEN_MISSING" });
        }

        try {
            const cookie = jwt.verify(token, Config.api.jwt?.secret ?? "") as { id: number };
            const userCtrl = new UserController((req as any).db);
            const user = await userCtrl.findUserById(cookie.id);

            if (user) {
                (req as any).user = user;
                return next();
            } else if (optional) {
                (req as any).user = null;
                return next();
            } else {
                return next({ status: 401, message: "INVALID_TOKEN" });
            }
        } catch (error: any) {
            if (optional) {
                (req as any).user = null;
                return next();
            } else if (error.name === "TokenExpiredError") {
                return next({ status: 401, message: "TOKEN_EXPIRED" });
            } else {
                return next({ status: 401, message: "INVALID_TOKEN" });
            }
        }
    };
}
