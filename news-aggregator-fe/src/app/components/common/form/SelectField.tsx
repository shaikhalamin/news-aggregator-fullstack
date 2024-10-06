import React, { ChangeEvent } from "react";
import { Form, FormText } from "react-bootstrap";
import { useFormContext } from "react-hook-form";

type GenericArray = {
  id: number | string;
  name: string;
};

interface SelectFormProps {
  labelText: string;
  fieldName: string;
  selectData: GenericArray[];
  errorMessage?: string;
  labelCls?: string;
  selectOnChange?: (value: string) => void;
}

const SelectField: React.FC<SelectFormProps> = ({
  labelText,
  fieldName,
  selectData,
  errorMessage,
  labelCls,
  selectOnChange,
  ...props
}) => {
  const { setValue } = useFormContext();

  const onChangeSelect = (elem: ChangeEvent<HTMLSelectElement>) => {
    const value = elem.target.value
    setValue(fieldName, value)
    selectOnChange && selectOnChange(value);
  };

  return (
    <Form.Group controlId={`htmlId${fieldName.toLowerCase()}`}>
      <Form.Label className={labelCls}>{labelText}</Form.Label>
      <Form.Select
        defaultValue={"Select"}
        onChange={(value) => onChangeSelect(value)}
        name={fieldName}
        className={errorMessage ? "is-invalid" : ""}
        {...props}
      >
        <option value={0}>--Select--</option>
        {selectData.map((item, index) => {
          return (
            <option key={index} value={item.id}>
              {item.name}
            </option>
          );
        })}
      </Form.Select>
      {errorMessage && (
        <FormText className="text-danger">{errorMessage}</FormText>
      )}
    </Form.Group>
  );
};

export default SelectField;
