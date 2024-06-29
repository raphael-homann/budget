import Entity from "@efrogg/synergy/Data/Entity";
import Budget from "./Budget";
import Envelope from "./Envelope";
// --imports--  ! keep this line

export default class Category extends Entity {

    public name: string = '';
    private _budgetId: string | null = null;
    private _budget: Budget | null = null;
    private _envelopeId: string | null = null;
    private _envelope: Envelope | null = null;

    // ---properties---  ! keep this line

    public get budgetId(): string | null {
        return this._budgetId;
    }

    public set budgetId(value: string | null) {
        this._budgetId = value;
        this._budget = null;
    }

    public get budget(): Budget | null {
        return this._budget ??= this.getRelation(Budget, this.budgetId);
    }

    public get envelopeId(): string | null {
        return this._envelopeId;
    }

    public set envelopeId(value: string | null) {
        this._envelopeId = value;
        this._envelope = null;
    }

    public get envelope(): Envelope | null {
        return this._envelope ??= this.getRelation(Envelope, this.envelopeId);
    }

    // ---methods--- ! keep this line
}
