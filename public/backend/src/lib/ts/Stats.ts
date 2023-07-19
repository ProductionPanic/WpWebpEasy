import { writable, type Writable } from "svelte/store";
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

    public static get stats(): Writable<{
        [key: string]: [string, number|string]
    }> {
        this._has_listeners = true;
        this.getStats();
        return this._stats;
    }

    public static updateStats(): void {
        this.getStats();
    }
}   

export const Stats = StatsHandler.stats;