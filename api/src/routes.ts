import authRoutes from "./app/auth/auth.routes";
import { Router } from "./routing/router";

const router = new Router();

router.register("/", authRoutes);

// router.get({
//     path: "/favicon.ico",
//     handler: (req, res) => {
//         const iconPath = path.join(__dirname, "static", "favicon.ico");
//         res.sendFile(iconPath);
//     }
// })

router.handle((req, res, next) => next({ status: 404 }));

export default router.expressRouter;
