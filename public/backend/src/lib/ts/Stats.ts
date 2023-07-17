import { writable } from "svelte/store";
import WebpEasy from "./WebpEasy";

export class StatsHandler {
    private static _stats = writable({});
    private static _statsInterval: number = 10000;
    private static _has_listeners: boolean = false;

    public static init(interval:number|null = null): void {
        if (interval) {
            StatsHandler._statsInterval = interval;
        }
        setInterval(() => {
            if(!StatsHandler._has_listeners) return
            this.getStats();
        }, StatsHandler._statsInterval);
    }

    private static getStats(): void {
        WebpEasy.get('stats').then((response) => {
            this._stats.set(response);
        });
    }

    public static get stats(): any {
        this._has_listeners = true;
        setTimeout(() => this.getStats(), 1);
        return this._stats;
    }
}   

export const Stats = StatsHandler.stats;