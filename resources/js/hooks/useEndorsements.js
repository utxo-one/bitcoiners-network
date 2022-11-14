import { ENDORSEMENT_TYPES } from "../utils/Types";

export default function useEndorsements() {

  const loadEndorsements = async twitter_username => {
    const { data: endorsements } = await axios.get(`/frontend/endorsements/user/${twitter_username}`);
    const { data: authEndorsements } = await axios.get(`/frontend/endorsements/user/${twitter_username}/auth`);

    const sortCount = ([, countA], [, countB]) => countA > countB ? -1 : 1;

    if (!endorsements?.endorsement_data) {
      return {};
    }

    // Because endorsements are cached, refreshing a page might seem to show that the user has
    // no endorsements, so iterate the array of auth user endorsements, and if current user has
    // endorsed this user, set the main endorsements to 1 so it's visible:
    Object.keys(ENDORSEMENT_TYPES).forEach(type => {
      if (endorsements.endorsement_data[type] === 0 && authEndorsements.endorsement_data[type] > 0) {
        endorsements.endorsement_data[type] = 1;
      }
    });

    return {
      endorsements      : Object.fromEntries(Object.entries(endorsements.endorsement_data)?.sort(sortCount)),
      endorsements_auth : authEndorsements.endorsement_data,
    }
  }

  return { loadEndorsements };
}
