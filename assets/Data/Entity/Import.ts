import Entity from "@efrogg/synergy/Data/Entity";
import Budget from "./Budget";
// --imports--  ! keep this line

export default class Import extends Entity  {

    public fileName: string = '';
    private _budgetId: string | null = null;
    private _budget: Budget | null = null;
    // ---properties---  ! keep this line

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
