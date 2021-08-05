import { React } from "react";
import { Redirect, Route, Switch } from "react-router-dom";
import { I18nextProvider } from "react-i18next";
import i18next from "i18next";
import Container from "react-bootstrap/Container";
import Row from "react-bootstrap/Row";
import Col from "react-bootstrap/Col";
import TagList from "./components/tag-list/tag-list";
import TopBar from "./components/navigation/topbar/topbar";
import SideBar from "./components/navigation/sidebar/sidebar";
import ScreenList from "./components/screen-list/screen-list";
import CategoryList from "./components/category-list/category-list";
import SlidesList from "./components/slides-list/slides-list";
import EditTag from "./components/edit-tag/edit-tag";
import EditScreen from "./components/edit-screen/edit-screen";
import EditCategories from "./components/edit-categories/edit-category";
import GroupsList from "./components/groups-list/groups-list";
import EditGroup from "./components/edit-group/edit-group";
import LocationsList from "./components/locations-list/locations-list";
import EditLocation from "./components/edit-location/edit-location";
import EditSlide from "./components/edit-slide/edit-slide";
import PlaylistsList from "./components/playlists-list/playlists-list";
import EditPlaylist from "./components/edit-playlist/edit-playlist";
import MediaList from "./components/media-list/media-list";
import EditMedia from "./components/edit-media/edit-media";
import commonDa from "./translations/da/common.json";
import "./app.scss";

/**
 * App component.
 *
 * @returns {object}
 * The component.
 */
function App() {
  i18next.init({
    interpolation: { escapeValue: false }, // React already does escaping
    lng: "da", // language to use
    resources: {
      da: {
        common: commonDa,
      },
    },
  });

  return (
    <>
      <I18nextProvider i18n={i18next}>
        <Container fluid className="h-100">
          <Row>
            <TopBar />
          </Row>
          <Row>
            <SideBar />
            <Col>
              <main>
                <Switch>
                  <Route path="/tags" component={TagList} />
                  <Route path="/screens" component={ScreenList} />
                  <Route path="/categories" component={CategoryList} />
                  <Route path="/locations" component={LocationsList} />
                  <Route path="/groups" component={GroupsList} />
                  <Route path="/tag/:id" component={EditTag} />
                  <Route path="/category/:id" component={EditCategories} />
                  <Route path="/group/:id" component={EditGroup} />
                  <Route path="/screen/:id" component={EditScreen} />
                  <Route path="/location/:id" component={EditLocation} />
                  <Route path="/slides" component={SlidesList} />
                  <Route path="/playlists" component={PlaylistsList} />
                  <Route path="/media-list" component={MediaList} />
                  <Route path="/playlist/:id" component={EditPlaylist} />
                  <Route path="/slide/:id" component={EditSlide} />
                  <Route path="/media/:id" component={EditMedia} />
                  <Redirect from="/" to="/tags" exact />
                </Switch>
              </main>
            </Col>
          </Row>
        </Container>
      </I18nextProvider>
    </>
  );
}

export default App;
