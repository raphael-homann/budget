import Entity from "@efrogg/synergy/Data/Entity";
import Category from "./Category";
// --imports--  ! keep this line

export default class DetectionMask extends Entity  {

    public mask: string = '';
    private _categoryId: string | null = null;
    private _category: Category | null = null;
    public score: number = 0;
    public active: boolean = true;
    // ---properties---  ! keep this line

    public get categoryId(): string | null {
        return this._categoryId;
    }
    public set categoryId(value: string | null) {
        this._categoryId = value;
        this._category = null;
    }
    public get category(): Category | null {
        return this._category ??= this.getRelation(Category,this.categoryId);
    }
    // ---methods--- ! keep this line
}
