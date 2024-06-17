import Entity from "@efrogg/synergy/Data/Entity";
import Budget from "./Budget";
// --imports--  ! keep this line

export default class Envelope extends Entity  {

    public name: string = '';
    public budgetId: string = '';
    private _budget: Budget | null = null;
    // ---properties---  ! keep this line

    public get budget(): Budget | null {
        console.log('get budget' + this.budgetId);
        return this._budget ??= this.getRelation(Budget,this.budgetId);
    }    
    // ---methods--- ! keep this line
}
