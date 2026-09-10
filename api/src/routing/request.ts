import { Request as ExpressRequest } from "express";
import { User } from "../app/user/user";
import { DatabaseInstance } from "../utils/database/database-instance";
import { Logger } from "../utils/logger";

export interface Request extends ExpressRequest {
    db: DatabaseInstance;
    logger: Logger;
    user: User;
}
