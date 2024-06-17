import EntityManager from "@efrogg/synergy/Data/EntityManager";
import RepositoryManager from "@efrogg/synergy/Data/RepositoryManager";
import Budget from "../Data/Entity/Budget";
import Envelope from "../Data/Entity/Envelope";

export const globalEntityManager: EntityManager = new EntityManager(
    new RepositoryManager([Budget,Envelope])
);
