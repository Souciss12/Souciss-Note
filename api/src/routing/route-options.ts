import { NextFunction } from "./next-function";
import { Request } from "./request";
import { Response } from "./response";

interface BaseRouteOptions {
    path: string | string[];
    validate?: Record<string, RegExp>;
    handler: (req: Request, res: Response, next: NextFunction) => void;
}

interface AuthRouteOptions extends BaseRouteOptions {
    auth: true;
    permissions?: string[];
}

interface PublicRouteOptions extends BaseRouteOptions {
    auth?: false;
}

export type RouteOptions = AuthRouteOptions | PublicRouteOptions;
