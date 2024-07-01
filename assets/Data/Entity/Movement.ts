import Entity from "@efrogg/synergy/Data/Entity";
import Category from "./Category";
import Budget from "./Budget";
import Import from "./Import";
// --imports--  ! keep this line

export default class Movement extends Entity  {

    public amount: string = '';
    private _category: Category | null = null;
    public comment: string = '';
    public date: Date = new Date();
    private _budget: Budget | null = null;

    private _categoryId: string | null = null;
    private _budgetId: string | null = null;
    private _importId: string | null = null;
    private _import: Import | null = null;
    // ---properties---  ! keep this line

    public get category(): Category | null {
        return this._category ??= this.getRelation(Category, this.categoryId);
    }

    public get budget(): Budget | null {
        return this._budget ??= this.getRelation(Budget, this.budgetId);
    }

    public get categoryId(): string | null {
        return this._categoryId;
    }

    public set categoryId(value: string | null) {
        this._categoryId = value;
        this._category = null;
    }

    public get budgetId(): string | null {
        return this._budgetId;
    }

    public set budgetId(value: string | null) {
        this._budgetId = value;
        this._budget = null;
    }
    public get importId(): string | null {
        return this._importId;
    }
    public set importId(value: string | null) {
        this._importId = value;
        this._import = null;
    }
    public get import(): Import | null {
        return this._import ??= this.getRelation(Import,this.importId);
    }
    // ---methods--- ! keep this line
}
