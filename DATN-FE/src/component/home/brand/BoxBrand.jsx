import { NavLink } from "react-router-dom";

export default function BoxBrand({ item, delay, dataaos }) {
  return (
    <div
      data-aos={dataaos}
      data-aos-delay="200"
      className="flex justify-center py-4 md:py-8"
    >
      <NavLink to={"/san-pham?brand=" + item.slug}>
        <img
          src={`${import.meta.env.VITE_ENDPOINT}${item.logo}`}
          className="w-28"
          alt={item.name}
        />
      </NavLink>
    </div>
  );
}
