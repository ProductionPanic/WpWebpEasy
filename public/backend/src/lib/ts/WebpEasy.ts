declare global {
    interface Window {
        WebpEasy: {
            nonce: string;
            restUrl: string;
            data: {
                links: {
                    settings: string;
                    home: string;
                },
                [key: string]: any;
            }
        }
    }
}

export default class WebpEasy {
    public static nonce: string;
    public static restUrl: string;
    public static data: any;

    public static init(): void {
        this.nonce = window.WebpEasy.nonce;
        this.restUrl = window.WebpEasy.restUrl;
        this.data = window.WebpEasy.data;
    }

    public static get(action: string): Promise<any> {
        if(!this.restUrl) this.init();
        if (!action.endsWith('/')) action = action.concat('/');
        if (!action.startsWith('/')) action = '/'.concat(action);
        return fetch(`${this.restUrl}${action}`, {
            method: 'GET',
            headers: {
                'Content-Type': 'application/json',
                'X-WP-Nonce': this.nonce
            }
        }).then((response) => {
            return response.json();
        });
    }

    public static post(action: string, data: any = {}): Promise<any> {
        if (!action.endsWith('/')) action = action.concat('/');
        if (!action.startsWith('/')) action = '/'.concat(action);
        return fetch(`${this.restUrl}${action}`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-WP-Nonce': this.nonce
            },
            body: JSON.stringify(data)
        }).then(async (response) => {
            return {
                success: response.ok,
                status: response.status,
                data: await response.json()
            }
        }).catch((error) => {
            return {
                success: false,
                status: 500,
                data: error
            }
        });
    }

    public static links() {
        return this.data.links;
    }

    public static getSettings() {
        return this.get('settings');
    }

    public static setSettings(data: any) {
        return this.post('settings', data);
    }

}