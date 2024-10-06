import { UserFeed } from "@/app/types/user/UserFeed";
import React from "react";
import { Row, Col, Card, Badge, Stack } from "react-bootstrap";
import DOMPurify from "dompurify";
import Link from "next/link";

type SingleFeedType = {
  feed: UserFeed;
};

const SingleNewsItem: React.FC<SingleFeedType> = ({ feed }) => {
  const imgSrc = feed?.image_url
    ? feed?.image_url
    : "https://images.unsplash.com/photo-1502772066658-3006ff41449b?q=80&w=1893&auto=format&fit=crop&ixlib=rb-4.0.3&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D";

  const feedContent = feed?.content
    ? `${feed?.content?.slice(0, 450)}....`
    : "";
  const sanitizedHtmlContent = DOMPurify.sanitize(feedContent);

  return (
    <Row className="py-1 px-1 mt-3">
      <Col md="5" className="mt-1 mb-1">
        <Card className="rounded-0">
          <Card.Body className="position-relative py-0 px-0">
            {/*eslint-disable-next-line @next/next/no-img-element*/}
            <img
              src={imgSrc as string}
              alt={feed.title as string}
              className={`w-100 object-fit`}
              height={250}
            />
          </Card.Body>
        </Card>
      </Col>
      <Col md="7" className="border-bottom">
        <Link href={feed.news_url as string} className="noDecoration">
          <Card className="border-0">
            <Row className="py-2 px-1">
              <Col className="mt-2 mb-3">
                <div className="mt-2 mb-1 text-color-a3a fw-bold">
                  <Row>
                    <Col
                      lg="12"
                      md="12"
                      sm="12"
                      xs="12"
                      className="text-start ft-20"
                    >
                      {feed?.author && (
                        <Stack direction="horizontal" gap={2}>
                          <Badge pill bg="secondary">
                            {feed?.author}
                          </Badge>
                        </Stack>
                      )}
                    </Col>
                  </Row>
                  <Row>
                    <Col
                      lg="12"
                      md="12"
                      sm="12"
                      xs="12"
                      className="text-start ft-20"
                    >
                      {feed.title}
                    </Col>
                  </Row>
                </div>

                <div
                  dangerouslySetInnerHTML={{ __html: sanitizedHtmlContent }}
                  className="ft-14 mt-2 mb-1 text-color-b94"
                ></div>

                <div className="mt-2">
                  <Row className="">
                    <Col md="12" className="text-start fs-14 fw-bold text-dark">
                      <Stack direction="horizontal" gap={2}>
                        <Badge pill bg="secondary">
                          {feed?.response_source}
                        </Badge>
                        <Badge pill bg="secondary">
                          {feed?.category}
                        </Badge>
                        <Badge pill bg="secondary">
                          {feed?.published_at}
                        </Badge>
                      </Stack>
                    </Col>
                  </Row>
                </div>
              </Col>
            </Row>
          </Card>
        </Link>
      </Col>
    </Row>
  );
};

export default SingleNewsItem;
