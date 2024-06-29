import EntityManager from "@efrogg/synergy/Data/EntityManager";
import RepositoryManager from "@efrogg/synergy/Data/RepositoryManager";
import Budget from "../Data/Entity/Budget";
import Envelope from "../Data/Entity/Envelope";
import Category from "../Data/Entity/Category";
import Movement from "../Data/Entity/Movement";

export const globalEntityManager: EntityManager = new EntityManager(
    new RepositoryManager([Budget,Envelope, Category, Movement])
);
