import React from "react";
import { Pagination } from "react-bootstrap";

type CustomPaginationProps = {
  currentPage: number;
  totalPages: number;
  onPageChange: (pageNumber: number) => void;
};

const CustomPagination: React.FC<CustomPaginationProps> = ({
  currentPage,
  totalPages,
  onPageChange,
}) => {
  const getPaginationItems = () => {
    const items = [];
    const maxPageDisplay = 5;
    const middlePages = 3;

    items.push(
      <Pagination.Prev
        key="prev"
        onClick={() => onPageChange(currentPage - 1)}
        disabled={currentPage === 1}
      />
    );

    // First Page
    items.push(
      <Pagination.Item
        key={1}
        onClick={() => onPageChange(1)}
        active={currentPage === 1}
      >
        1
      </Pagination.Item>
    );

    // Left Ellipsis
    if (currentPage > middlePages + 1) {
      items.push(<Pagination.Ellipsis key="left-ellipsis" disabled />);
    }

    // Middle Pages
    const startPage = Math.max(2, currentPage - 1);
    const endPage = Math.min(totalPages - 1, currentPage + 1);

    for (let i = startPage; i <= endPage; i++) {
      items.push(
        <Pagination.Item
          key={i + 99}
          onClick={() => onPageChange(i)}
          active={currentPage === i}
        >
          {i}
        </Pagination.Item>
      );
    }

    // Right Ellipsis
    if (currentPage < totalPages - middlePages) {
      items.push(<Pagination.Ellipsis key="right-ellipsis" disabled />);
    }

    // Last Page
    items.push(
      <Pagination.Item
        key={totalPages}
        onClick={() => onPageChange(totalPages)}
        active={currentPage === totalPages}
      >
        {totalPages}
      </Pagination.Item>
    );

    // Add Next Icon
    items.push(
      <Pagination.Next
        key="next"
        onClick={() => onPageChange(currentPage + 1)}
        disabled={currentPage === totalPages}
      />
    );

    return items;
  };

  return <Pagination>{getPaginationItems()}</Pagination>;
};

export default CustomPagination;
