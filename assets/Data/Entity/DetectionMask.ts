import Entity from "@efrogg/synergy/Data/Entity";
import Category from "./Category";
import Budget from "./Budget";
// --imports--  ! keep this line

export default class DetectionMask extends Entity  {

    public mask: string = '';
    private _categoryId: string | null = null;
    private _category: Category | null = null;
    public score: number = 0;
    public active: boolean = true;
    public name: string = '';
    private _budgetId: string | null = null;
    private _budget: Budget | null = null;
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
    public get budgetId(): string | null {
        return this._budgetId;
    }
    public set budgetId(value: string | null) {
        this._budgetId = value;
        this._budget = null;
    }
    public get budget(): Budget | null {
        return this._budget ??= this.getRelation(Budget,this.budgetId);
    }
    // ---methods--- ! keep this line
}
