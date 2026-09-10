import { HttpError } from '../utils/http-error';

export type NextFunction = (err?: number | HttpError) => void;
