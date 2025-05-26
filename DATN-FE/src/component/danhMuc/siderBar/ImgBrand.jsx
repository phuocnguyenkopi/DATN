import { useSearchParams } from "react-router-dom";
import BtnAnimation from "../../shared/BtnAnimation";

export default function ImgBrand({ item }) {
  const [searchParams, setSearchParams] = useSearchParams();

  const slugBrand = searchParams.get("brand") || "";
  // Handler khi nhấp vào loại sản phẩm
  function handleSetSlug(value) {
    const newSearchParams = new URLSearchParams(searchParams.toString());
    if (slugBrand === value) {
      newSearchParams.delete("brand");
    } else {
      newSearchParams.delete("page");
      newSearchParams.delete("category");
      newSearchParams.set("brand", value);
    }
    setSearchParams(newSearchParams);
  }

  return (
    <>
      <BtnAnimation>
        <button
          onClick={() => handleSetSlug(item.slug)}
          className={`block transform rounded border border-gray-200 bg-white p-2 transition-all duration-200 hover:border-primary hover:shadow-2xl ${item.slug == slugBrand ? "border-2 border-secondary" : ""}`}
        >
          <img
            src={`${import.meta.env.VITE_ENDPOINT}${item.logo}`}
            className="h-4 w-full object-cover"
            alt=""
          />
        </button>
      </BtnAnimation>
    </>
  );
}
