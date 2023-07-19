import WebpEasy from "./WebpEasy";

export class ImagesHandler {
    public static get_images(limit:number=100, skip:number=0): Promise<any> {
        return WebpEasy.get('images/max/'+limit + '/skip/' + skip);
    }

    public static get_image(id:number): Promise<any> {
        return WebpEasy.get('image/'+id);
    }

    public static convert_images(ids:number[]): Promise<any> {
        return WebpEasy.post('images/convert', {ids: ids});
    }
}