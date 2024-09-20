import Entity from "@efrogg/synergy/Data/Entity";
import Budget from "./Budget";
import Envelope from "./Envelope";
import DetectionMask from "./DetectionMask";
// --imports--  ! keep this line

export default class Category extends Entity  {

    public name: string = '';
    private _budgetId: number | null = null;
    private _budget: Budget | null = null;
    private _envelopeId: number | null = null;
    private _envelope: Envelope | null = null;

    // ---properties---  ! keep this line

    public get budgetId(): number | null {
        return this._budgetId;
    }

    public set budgetId(value: number | null) {
        this._budgetId = value;
        this._budget = null;
    }

    public get budget(): Budget | null {
        return this._budget ??= this.getRelation(Budget, this.budgetId);
    }

    public get envelopeId(): number | null {
        return this._envelopeId;
    }

    public set envelopeId(value: number | null) {
        this._envelopeId = value;
        this._envelope = null;
    }

    public get envelope(): Envelope | null {
        return this._envelope ??= this.getRelation(Envelope, this.envelopeId);
    }

    public get detectionMasks(): DetectionMask[] {
        return this.getOneToMany(DetectionMask, 'categoryId');
    }
    public set detectionMasks( masks    )
    {
        console.warn('detectionMasks is a read only property');
    }
    // ---methods--- ! keep this line

    hasDetectionMasks() {
        return this.detectionMasks.length > 0;
    }
}
