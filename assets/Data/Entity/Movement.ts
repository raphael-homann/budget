import Entity from "@efrogg/synergy/Data/Entity";
import Category from "./Category";
import Budget from "./Budget";
// --imports--  ! keep this line

export default class Movement extends Entity {

    public amount: string = '';
    public categoryId: string = '';
    private _category: Category | null = null;
    public comment: string = '';
    public date: Date = new Date();
    public budgetId: string = '';
    private _budget: Budget | null = null;

    // ---properties---  ! keep this line

    public get category(): Category | null {
        return this._category ??= this.getRelation(Category, this.categoryId);
    }

    public get budget(): Budget | null {
        return this._budget ??= this.getRelation(Budget, this.budgetId);
    }

    // ---methods--- ! keep this line
}
