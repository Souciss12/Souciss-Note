import { Router as ExpressRouter, RequestHandler } from "express";
import authentication from "../middlewares/authentication";
import paramValidation from "../middlewares/param-validation";
import { RouteOptions } from "./route-options";

export class Router {

    public readonly expressRouter: ExpressRouter;

    public constructor() {
        this.expressRouter = ExpressRouter();
    }

    public handle(handler: RequestHandler): void {
        this.expressRouter.use(handler);
    }

    public register(path: string, router: Router): void {
        this.expressRouter.use(path, router.expressRouter);
    }

    private createRoute(method: "GET" | "POST" | "PUT" | "DELETE" | "PATCH", options: RouteOptions) {
        const middlewares: RequestHandler[] = [];

        if (options.validate) {
            middlewares.push(paramValidation(options.validate));
        }

        middlewares.push(authentication(!options.auth));

        (this.expressRouter as any)[method.toLowerCase()](
            options.path,
            ...middlewares,
            options.handler
        );
    }

    public get(options: RouteOptions): void {
        this.createRoute("GET", options);
    }

    public post(options: RouteOptions): void {
        this.createRoute("POST", options);
    }

    public put(options: RouteOptions): void {
        this.createRoute("PUT", options);
    }

    public patch(options: RouteOptions): void {
        this.createRoute("PATCH", options);
    }

    public delete(options: RouteOptions): void {
        this.createRoute("DELETE", options);
    }
}
