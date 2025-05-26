import ProductCard from "../../shared/ProductCard";
import ButtonSeeAll from "../../shared/ButtonSeeAll";
import UIGrid from "../../shared/UIGrid";
import { useFetch } from "../../../hook/useFeach";
import { fetchProductBestSellingBike } from "../../../api/home";

function XepDap({ state }) {
  const { fetchedData } = useFetch(fetchProductBestSellingBike, []);

  if (!state) return;
  return (
    <>
      <UIGrid>
        {fetchedData?.length > 0 &&
          fetchedData.map((item, index) => (
            <ProductCard
              key={item.id}
              product={item}
              delay={index}
              dataaos="fade-up-left"
            />
          ))}
      </UIGrid>

      <ButtonSeeAll link="/san-pham?ct=best-selling" />
    </>
  );
}
export default XepDap;
