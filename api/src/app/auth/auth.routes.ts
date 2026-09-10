import jwt from "jsonwebtoken";
import { Router } from "../../routing/router";
import { Config } from "../../utils/config/config";
import { UserController } from "../user/user.controller";

const authRoutes = new Router();

authRoutes.get({
    path: "/auth",
    auth: false,
    handler: async (req, res) => {
        res.json({
            authenticated: !!req.user,
            user: req.user ?? null
        });
    }
});

authRoutes.post({
    path: "/auth",
    auth: false,
    handler: async (req, res, next) => {
        const { login, password } = req.body;
        if (!login || !password) {
            return next(400);
        }

        const userCtrl = new UserController(req.db);
        const user = await userCtrl.findUserByLoginAndPassword(login, password);

        if (!user) {
            return next(401);
        }

        req.user = user;

        const jwtConfig = Config.api.jwt;
        const token = jwt.sign({ id: user.id }, jwtConfig?.secret ?? "", { expiresIn: jwtConfig?.expiresIn ?? "1h" });

        const cookieConfig = Config.api.cookie;
        res.cookie("jwt", token, {
            httpOnly: cookieConfig?.httpOnly ?? true,
            secure: cookieConfig?.secure ?? true,
            sameSite: cookieConfig?.sameSite ?? "strict",
            maxAge: cookieConfig?.maxAge ?? 3600000
        });

        res.status(204).json();
    }
});

authRoutes.delete({
    path: "/auth",
    auth: false,
    handler: (req, res) => {
        res.clearCookie("jwt");
        res.status(204).json();
    }
});

export default authRoutes;
